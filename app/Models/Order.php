<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Models;

use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Traits\Exchange\MarketTrait;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected string $market;
    protected string $type;
    protected string $side;
    protected float $original_quantity;
    protected float|null $original_price;
    protected float|null $stop_price;
    protected bool $is_virtual;
    protected User $user;

    use MarketTrait, HasFactory;

    /**
     * @var array|string[]
     */

    protected $fillable = [
        'engine',
        'engine_order_id',
        'internal_order_id',
        'market',
        'type',
        'side',
        'executed_price',
        'original_price',
        'stop_price',
        'original_market_price',
        'original_quantity',
        'executed_quantity',
        'wage_amount',
        'fill_percentage',
        'cumulative_quote_quantity',
        'status',
        'is_virtual'
    ];
    /**
     * @var string[]
     */

    protected $appends = ['type_casted', 'side_casted', 'status_casted'];

    protected $casts = [
        'executed_price' => 'decimal:8',
        'original_price' => 'decimal:8',
        'stop_price' => 'decimal:8',
        'original_market_price' => 'decimal:8',
        'original_quantity' => 'decimal:8',
        'executed_quantity' => 'decimal:8',
        'wage_amount' => 'decimal:8',
        'fill_percentage' => 'decimal:2',
        'cumulative_quote_quantity' => 'decimal:8',
        'is_virtual' => 'boolean'
    ];

    /**
     * @param  User  $user
     * @param  string  $market
     * @param  float  $original_quantity
     * @param  float|null  $original_price
     * @param  float|null  $stopPrice
     * @param  string  $side
     * @param  string  $type
     * @param  bool  $is_virtual
     * @return $this
     */

    public function prepare(
        User $user,
        string $market,
        float $original_quantity,
        float|null $original_price,
        float|null $stopPrice,
        string $side,
        string $type,
        bool $is_virtual
    ): static {
        $this->user = $user;
        $this->market = $market;
        $this->original_quantity = $original_quantity;
        $this->original_price = $original_price;
        $this->stop_price = $stopPrice;
        $this->side = $side;
        $this->type = $type;
        $this->is_virtual = $is_virtual;
        return $this;
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws \JsonException
     * @throws NotFoundException
     */

    public function cancelOrder(): void
    {
        DB::beginTransaction();
        try {
            /**
             * Call Hermes Cancel endpoint.
             */

            $this->hermesOrder()->cancelOrder($this->attributes['engine_order_id']);

            /**
             * Calculations.
             */

            $marketExploded = explode('-', $this->attributes['market']);

            //Should simply use $this->user instead of performing first() and find() together!!!
            $user = User::find(self::user()->first()->id);
            if(!$user instanceof User){
                throw new NotFoundException(NotFoundException::USER_NOT_FOUND);
            }

            if ($this->attributes['side'] === 'SELL') {
                $qty = $this->attributes['original_quantity'] - $this->attributes['executed_quantity'];

                $user->setWallet($marketExploded[0], 1, $this->attributes['is_virtual'])->chargeWallet($qty);

                $user->setWallet($marketExploded[0], 2, $this->attributes['is_virtual'])->chargeWallet(-$qty);
            } elseif ($this->attributes['side'] === 'BUY') {
                $qty = ((($this->attributes['original_price'] ?? $this->attributes['original_market_price']) * $this->attributes['original_quantity']) - $this->attributes['cumulative_quote_quantity']);

                $user->setWallet($marketExploded[1], 1, $this->attributes['is_virtual'])->chargeWallet($qty);

                $user->setWallet($marketExploded[1], 2, $this->attributes['is_virtual'])->chargeWallet(-$qty);
            }

            /**
             * Update status of order.
             */

            self::update([
                'status' => 'CANCELED'
            ]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            throw $exception;
        }
    }

    /**
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::Class);
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws \JsonException
     * @throws NotFoundException
     */

    public function updateOrder(): void
    {
        DB::beginTransaction();
        try {
            /**
             * Make inquiry call.
             */

            $orderLatestUpdate = $this->hermesOrder()->getOrder($this->attributes['engine_order_id']);

            /**
             * Seek for not submitted trades.
             */

            if (!isset($orderLatestUpdate['trades']) || !is_array($orderLatestUpdate['trades']) || count(
                    $orderLatestUpdate['trades']
                ) === 0) {
                return;
            }

            /**
             * Cool! We have trades to deal with.
             */

            foreach ($orderLatestUpdate['trades'] as $trade) {
                /**
                 * Continue if order exists.
                 */

                if (self::trades()->where('engine_trade_id', $trade->internal_trade_id)->exists()) {
                    continue;
                }

                /**
                 * Store trade in database.
                 */

                self::trades()->create(
                    collect($trade)->except('internal_trade_id')->put(
                        'engine_trade_id',
                        $trade->internal_trade_id
                    )->toArray()
                );

                /**
                 * Handle maths.
                 */

                $marketExploded = explode('-', $this->attributes['market']);

                //Should simply use $this->user instead of performing first() and find() together!!!
                $user = User::find(self::user()->first()->id);
                if(!$user instanceof User){
                    throw new NotFoundException(NotFoundException::USER_NOT_FOUND);
                }

                /**
                 * Update balances of user.
                 */

                if ($this->attributes['side'] === 'SELL') {
                    $executedAmount = $trade->cumulative_quote_quantity;

                    $wageAmount = $executedAmount * config('settings.trade_wage') / 100;

                    $wageCurrency = $marketExploded[1];

                    $user->setWallet($marketExploded[1], 1, $this->attributes['is_virtual'])->chargeWallet(
                        $executedAmount - $wageAmount
                    );

                    $user->setWallet($marketExploded[0], 2, $this->attributes['is_virtual'])->chargeWallet(
                        -$trade->quantity
                    );
                } elseif ($this->attributes['side'] === 'BUY') {
                    $executedAmount = $trade->quantity;

                    $wageAmount = $executedAmount * config('settings.trade_wage') / 100;

                    $wageCurrency = $marketExploded[0];

                    $user->setWallet($marketExploded[0], 1, $this->attributes['is_virtual'])->chargeWallet(
                        $executedAmount - $wageAmount
                    );

                    $user->setWallet($marketExploded[1], 2, $this->attributes['is_virtual'])->chargeWallet(
                        -($trade->cumulative_quote_quantity)
                    );
                }

                /**
                 * Handle wages and referrer rewards.
                 */

                $wageSymbol = Symbol::title($wageCurrency);

                if (!isset($wageAmount) || $wageAmount <= 0 || !$wageSymbol->exists()) {
                    continue;
                }

                //No need for checking null pointer
                $wageSymbol = $wageSymbol->first();

                /**
                 * Define amounts.
                 */

                $tradeWage = $wageAmount;

                if ($user->referrer()->exists()) {
                    $referrerReward = $tradeWage * config('settings.referral_reward') / 100;

                    $tradeWage -= $referrerReward;

                    $referrerUser = $user->referrer()->first();

                    $referrerUser->setWallet($wageSymbol->id, 1, $this->attributes['is_virtual'])->chargeWallet(
                        $referrerReward
                    );

                    /**
                     * Submit transaction.
                     */

                    $referrerUser->transactions()->create([
                        'symbol_id' => $wageSymbol->id,
                        'quantity' => $referrerReward,
                        'type' => 1,
                        'side' => 1
                    ]);
                }

                /**
                 * Give the trade wage to desired account of admin.
                 */

                if (config('settings.trade_wage_receiver_user_id')) {
                    /**
                     * Fetch destination user.
                     */

                    $destinationAccount = User::find(config('settings.trade_wage_receiver_user_id'));
                    if(!$destinationAccount instanceof User){
                        throw new NotFoundException(NotFoundException::USER_NOT_FOUND);
                    }

                    $destinationAccount->setWallet($wageSymbol->id, 1, $this->attributes['is_virtual'])->chargeWallet(
                        $tradeWage
                    );

                    /**
                     * Submit the transaction.
                     */

                    $destinationAccount->transactions()->create([
                        'quantity' => $tradeWage,
                        'symbol_id' => $wageSymbol->id,
                        'type' => 2,
                        'side' => 1,
                    ]);
                }
            }

            /**
             * Well played! and last step, Update the order :)).
             */
            if ($this->attributes['side'] == "BUY") {
                $wage = $orderLatestUpdate['executed_quantity'] * config('settings.trade_wage') / 100;
            } else {
                $wage = $orderLatestUpdate['executed_quantity'] * $orderLatestUpdate['executed_price'] * config(
                        'settings.trade_wage'
                    ) / 100;
            }

            self::update([
                'status' => $orderLatestUpdate['status'],
                'executed_price' => $orderLatestUpdate['executed_price'],
                'executed_quantity' => $orderLatestUpdate['executed_quantity'],
                'cumulative_quote_quantity' => $orderLatestUpdate['cumulative_quote_quantity'],
                'fill_percentage' => $orderLatestUpdate['executed_quantity'] / $orderLatestUpdate['original_quantity'] * 100,
                'wage_amount' => $wage,
            ]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            throw $exception;
        }
    }

    /**
     * @return HasMany
     */

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * @return Model
     * @throws GuzzleException
     * @throws \JsonException
     */

    public function submitOrder(): Model
    {
        DB::beginTransaction();
        try {
            /**
             * Make request to trade engine.
             */

            $engineOrder = $this->hermesOrder()->createOrder(
                $this->type,
                $this->side,
                $this->original_quantity,
                $this->market,
                $this->original_price,
                $this->stop_price,
                $this->is_virtual
            );

            /**
             * Subtract amounts based on order type.
             */

            $marketExploded = explode('-', $this->market);

            if ($this->side === 'SELL') {
                $qty = $this->original_quantity;

                $this->user->setWallet($marketExploded[0], 1, $this->is_virtual)->chargeWallet(-$qty);

                $this->user->setWallet($marketExploded[0], 2, $this->is_virtual)->chargeWallet($qty);
            } elseif ($this->side === 'BUY') {
                $qty = $this->original_quantity * ($this->original_price ?? $engineOrder['original_market_price']);

                $this->user->setWallet($marketExploded[1], 1, $this->is_virtual)->chargeWallet(-$qty);

                $this->user->setWallet($marketExploded[1], 2, $this->is_virtual)->chargeWallet($qty);
            }

            /**
             * Create order.
             */

            $order = $this->user->orders()->create([
                'engine_order_id' => $engineOrder['internal_order_id'],
                'engine' => $this->hermesOrder()->base,
                'original_market_price' => $engineOrder['original_market_price'],
                'type' => $this->type,
                'side' => $this->side,
                'market' => $this->market,
                'original_price' => $this->type === 'MARKET' ? 0 : $this->original_price ?? 0,
                'original_quantity' => $this->original_quantity,
                'stop_price' => $this->stop_price ?? 0,
                'is_virtual' => $this->is_virtual,
            ]);
            DB::commit();
            /**
             * Return order.
             */

            return $order->fresh();
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            throw $exception;
        }
    }

    /**
     * @return $this
     * @throws Exception
     */

    public function marketIsValid(): static
    {
        /**
         * Validate market.
         */

        if (!$this->getMarket($this->market)) {
            throw new Exception(__('messages.exchange.orders.store.invalidMarket'));
        }

        /**
         * Return.
         */

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */

    public function userHasSufficientBalance(): static
    {
        /**
         * Explode symbol.
         */

        $marketExploded = explode('-', $this->market);

        /**
         * Validate balances based on order type.
         */

        if ($this->side === 'SELL') {
            /**
             * src amount should be GTE $order->quantity.
             */

            $srcWallet = $this->user->setWallet($marketExploded[0], 1, $this->is_virtual)->getWallet();

            if ($srcWallet->quantity < $this->original_quantity) {
                throw new Exception(__('messages.exchange.orders.store.insufficientBalance'), 400);
            }
        } elseif ($this->side === 'BUY') {
            $dstWallet = $this->user->setWallet($marketExploded[1], 1, $this->is_virtual)->getWallet();

            if ($dstWallet->quantity < ($this->original_quantity * ($this->original_price ?? $this->getMarketPrice(
                            $this->market
                        )))) {
                throw new Exception(__('messages.exchange.orders.store.insufficientBalance'), 400);
            }
        }

        return $this;
    }

    /**
     * @return string
     */

    public function getTypeCastedAttribute(): string
    {
        return __('attributes.exchange.orders.types.'.$this->attributes['type']);
    }

    /**
     * @return string
     */

    public function getSideCastedAttribute(): string
    {
        return __('attributes.exchange.orders.sides.'.$this->attributes['side']);
    }

    /**
     * @return string
     */

    public function getStatusCastedAttribute(): string
    {
        return __('attributes.exchange.orders.status.'.$this->attributes['status']);
    }

    /**
     * @return string
     */

    public function getRouteKeyName(): string
    {
        return 'internal_order_id';
    }

    /**
     * @param $query
     * @param $status
     * @return mixed
     */

    public function scopeStatus($query, $status): mixed
    {
        return $query->whereStatus($status);
    }

    /**
     * @param $query
     * @param $side
     * @return mixed
     */

    public function scopeSide($query, $side): mixed
    {
        return $query->whereSide($side);
    }

    /**
     * @param $query
     * @param $type
     * @return mixed
     */

    public function scopeType($query, $type): mixed
    {
        return $query->whereType($type);
    }

    /**
     * @param $query
     * @param $currency
     * @return mixed
     */

    public function scopeCurrency($query, $currency): mixed
    {
        return $query->where('market', 'LIKE', '%'.$currency.'%');
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsActive($query): mixed
    {
        return $query->whereIn('status', ['NEW', 'PARTIALLY_FILLED', 'PENDING', 'PENDING_CANCELED'])->where(
            'fill_percentage',
            '!=',
            100
        );
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeIsCancelable($query): mixed
    {
        return $query->where('fill_percentage', '!=', 100)->whereNotIn(
            'status',
            ['FILLED', 'CANCELED', 'PENDING_CANCELED']
        );
    }

}

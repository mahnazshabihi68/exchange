<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Users\Exchange;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exchange\Order\Query;
use App\Http\Requests\Exchange\Order\Store;
use App\Models\Order;
use App\Models\User;
use App\Traits\Exchange\MarketTrait;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    use MarketTrait;

    public function __construct()
    {
        $this->middleware(['auth:user'])->except(['getCandles', 'markets']);

        $this->middleware(['permission:trade'])->only(['storeOrder', 'cancelOrder', 'cancelAllOrders']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function markets(): JsonResponse
    {
        try {

            return response()->json([
                'markets' => $this->getMarkets()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Store $request
     * @return JsonResponse
     * @throws GuzzleException
     * @throws \JsonException
     */

    public function storeOrder(Store $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $order = (new Order)->prepare(User::find($this->user()->id), $request->market, $request->original_quantity, $request->original_price ?? null, $request->stop_price ?? null, $request->side, $request->type, $request->is_virtual)
                ->marketIsValid()
                ->userHasSufficientBalance()
                ->submitOrder();

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'order' => $order,
                'message' => __('messages.exchange.orders.store.successful'),
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @return Authenticatable
     */

    private function user(): Authenticatable
    {
        return auth('user')->user();
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function getOrders(): JsonResponse
    {
        try {

            return response()->json([
                'orders' => $this->user()->orders()->with('trades')->latest()->get()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Order $order
     * @return JsonResponse
     * @throws \JsonException
     */

    public function getOrder(Order $order): JsonResponse
    {
        try {

            return response()->json([
                'order' => $this->user()->orders()->with('trades')->findOrFail($order->id)
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Query $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function query(Query $request): JsonResponse
    {
        try {

            /**
             * Define all orders.
             */

            $orders = $this->user()->orders();

            /**
             * Lets filter orders based on queries.
             */

            if ($request->created_after) {

                $orders = $orders->where('created_at', '>=', $request->created_after);

            }

            if ($request->created_before) {

                $orders = $orders->where('created_at', '<=', $request->created_before);

            }

            if ($request->status) {

                $orders = $orders->whereIn('status', $request->status);

            }

            if ($request->side) {

                $orders = $orders->side($request->side);

            }

            if ($request->type) {

                $orders = $orders->type($request->type);

            }

            if ($request->has('is_virtual')){

                $orders = $orders->where('is_virtual', $request->is_virtual);

            }

            /**
             * Return response.
             */

            return response()->json([
                'orders' => $orders->latest()->get()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Order $order
     * @return JsonResponse
     * @throws \JsonException
     */

    public function cancelOrder(Order $order): JsonResponse
    {
        try {

            $order = $this->user()->orders()->isCancelable()->findOrFail($order->id);

            /**
             * Update order.
             */

            $order->update([
                'status' => 'PENDING_CANCELED'
            ]);

            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.exchange.orders.cancel.received'),
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function cancelAllOrders(): JsonResponse
    {
        DB::beginTransaction();
        try {

            /**
             * Get all cancelable orders.
             */

            $cancelableOrders = $this->user()->orders()->isCancelable()->get();

            /**
             * Update orders.
             */

            foreach ($cancelableOrders as $cancelableOrder) {

                $cancelableOrder->update([
                    'status' => 'PENDING_CANCELED'
                ]);

            }

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.exchange.orders.cancel.received'),
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param string $market
     * @param string $timeframe
     * @return JsonResponse
     * @throws GuzzleException
     * @throws \JsonException
     */

    public function getCandles(string $market, string $timeframe): JsonResponse
    {
        try {

            return response()->json([
                'candlestick-data' => $this->hermesMarket()->getCandles($market, $timeframe)
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }

    }
}

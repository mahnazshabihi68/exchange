<?php

namespace App\Http\Controllers\Admins\Market;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Hermes\Market\Market;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Market\Store;
use App\Http\Requests\Admins\Market\Update;
use App\Traits\Exchange\MarketTrait;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

class MarketController extends Controller
{
    use MarketTrait;

    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:market']);
    }

    /**
     * @return JsonResponse
     * @throws GuzzleException
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {

            /**
             * return response.
             */

            return response()->json([
                'markets' => $this->hermesMarket()->getMarkets()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param string $market
     * @return JsonResponse
     * @throws GuzzleException
     * @throws \JsonException
     */

    public function show(string $market): JsonResponse
    {
        try {

            /**
             * return response.
             */

            return response()->json([
                'market' => $this->hermesMarket()->getMarket($market)
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

    public function store(Store $request): JsonResponse
    {
        $exchange = Market::setStaticExchange($request->market);

        try {

            $market = $this->hermesMarket()->createMarket(
                market: $request->market,
                isActive: $request->is_active,
                isInternal: $request->is_internal,
                isDirect: $request->is_direct,
                exchange: $exchange
            );

            /**
             * return response.
             */

            return response()->json([
                'market' => $market,
                'message' => __('messages.markets.store.successful')
            ], 201);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @param Update $request
     * @param string $market
     * @return JsonResponse
     * @throws GuzzleException
     * @throws \JsonException
     */

    public function update(Update $request, string $market): JsonResponse
    {
        $exchange = Market::setStaticExchange($request->market);

        try {

            $market = $this->hermesMarket()->updateMarket(
                market: $market,
                isActive: $request->is_active,
                isInternal: $request->is_internal,
                isDirect: $request->is_direct,
                exchange: $exchange
            );

            /**
             * return response.
             */

            return response()->json([
                'market' => $market,
                'message' => __('messages.markets.update.successful')
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }

    /**
     * @throws \JsonException
     */
    public function destroy(): JsonResponse
    {
        try {

            /**
             * return response.
             */

            return response()->json([

            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);

        }
    }
}

<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins;

use App\Classes\Binance;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExchangeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:exchange']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function rateLimits(Request $request): JsonResponse
    {
        /**
         * Validation.
         */

        $this->validate($request, [
            'exchange' => 'required|string|in:binance'
        ]);

        /**
         * Return response.
         */

        return response()->json([
            'rate-limits' => $this->exchange()->getRateLimits()
        ]);
    }

    /**
     * @return Binance
     */

    private function exchange(): Binance
    {
        return new Binance();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Exception
     */

    public function assets(Request $request): JsonResponse
    {
        /**
         * Validate request.
         */

        $this->validate($request, [
            'exchange' => 'required|string|in:binance'
        ]);

        /**
         * Return response.
         */

        return response()->json([
            'assets' => $this->exchange()->assets()
        ]);
    }

    public function account()
    {

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function allOrders(Request $request): JsonResponse
    {
        /**
         * Validation
         */

        $this->validate($request, [
            'exchange' => 'required|string|in:binance',
            'symbol' => 'required|string'
        ]);

        /**
         * Return response.
         */

        return response()->json([
            'all-orders' => $this->exchange()->allOrders($request->symbol)
        ]);
    }
}

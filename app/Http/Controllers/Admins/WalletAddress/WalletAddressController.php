<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins\WalletAddress;

use App\Exceptions\Primary\NotFoundException;
use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\WalletAddress\Store;
use App\Http\Requests\Admins\WalletAddress\Update;
use App\Models\Blockchain;
use App\Models\WalletAddress;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use Symfony\Component\HttpFoundation\Response;

use function __;
use function response;

class WalletAddressController extends Controller
{
    /**
     * WalletAddressController constructor.
     */

    public function __construct()
    {
         $this->middleware(['auth:admin', 'permission:wallet-address']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {
            return response()->json([
                'wallet-addresses' => WalletAddress::with('user', 'blockchain')->latest()->get()
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param  WalletAddress  $walletAddress
     * @return JsonResponse
     * @throws \JsonException
     */

    public function show(WalletAddress $walletAddress): JsonResponse
    {
        try {
            return response()->json([
                'wallet-address' => $walletAddress->with('user', 'blockchain')->findOrFail($walletAddress->id)
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param  Store  $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function store(Store $request): JsonResponse
    {
        try {
            $walletAddress = Blockchain::findOrFail($request->blockchain_id)->walletAddresses()->create(
                $request->except(['blockchain_id'])
            );

            return response()->json([
                'wallet-address' => $walletAddress,
                'message' => __('messages.WalletAddresses.store.successful')
            ], 201);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param  Update  $request
     * @param  WalletAddress  $walletAddress
     * @return JsonResponse
     * @throws \JsonException
     */

    public function update(Update $request, WalletAddress $walletAddress): JsonResponse
    {
        DB::beginTransaction();
        try {
            $walletAddress->update($request->except(['blockchain_id', 'private_key', 'address']));

            $walletAddress->blockchain()->associate($request->blockchain_id)->save();

            DB::commit();

            return response()->json([
                'message' => __('messages.WalletAddresses.update.successful'),
                'wallet-address' => $walletAddress->fresh()
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
     * @param  WalletAddress  $walletAddress
     * @return JsonResponse
     * @throws \JsonException
     */

    public function destroy(WalletAddress $walletAddress): JsonResponse
    {
        try {
            if ($walletAddress->user()->exists()) {
                throw new Exception(__('messages.WalletAddresses.isAllocated'));
            }

            $walletAddress->delete();

            return response()->json([
                'message' => __('messages.WalletAddresses.destroy.successful')
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}

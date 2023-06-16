<?php


/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Http\Controllers\Users\BankAccount;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\BankAccount\Store;
use App\Http\Requests\Users\BankAccount\Update;
use App\Models\bankAccount;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class BankAccountController extends Controller
{
    /**
     * BankAccountController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:user']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {

            return response()->json([
                'bankAccounts' => $this->user()->bankAccounts()->latest()->get()
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
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
     * @param bankAccount $bankAccount
     * @return JsonResponse
     * @throws \JsonException
     */

    public function show(bankAccount $bankAccount): JsonResponse
    {
        try {

            return response()->json([
                'bankAccount' => $bankAccount
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
     * @throws \JsonException
     */

    public function store(Store $request): JsonResponse
    {
        try {
            $bankAccount = $this->user()->bankAccounts()->create($request->all());
            return response()->json([
                'bank-account' => $bankAccount,
                'message' => __('messages.bankAccounts.store.successful'),
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
     * @param bankAccount $bankAccount
     * @return JsonResponse
     * @throws \JsonException
     */

    public function update(Update $request, bankAccount $bankAccount): JsonResponse
    {
        try {
            $bankAccount->update($request->all());
            return response()->json([
                'bank-account' => $bankAccount->fresh(),
                'message' => __('messages.bankAccounts.update.successful')
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param bankAccount $bankAccount
     * @return JsonResponse
     * @throws \JsonException
     */

    public function destroy(bankAccount $bankAccount): JsonResponse
    {
        try {
            $bankAccount->delete();

            return response()->json([
                'message' => __('messages.bankAccounts.destroy.successful')
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}

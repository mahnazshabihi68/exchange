<?php

namespace App\Http\Controllers\Admins\Symbol;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Symbol\Store;
use App\Http\Requests\Admins\Symbol\Update;
use App\Models\Symbol;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SymbolController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:symbol']);
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function list(): JsonResponse
    {
        try {
            /**
             * return response.
             */

            return response()->json([
                'symbols' => Symbol::with('blockchains')->latest()->get()
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Symbol $symbol
     * @return JsonResponse
     * @throws \JsonException
     */

    public function show(Symbol $symbol): JsonResponse
    {
        try {
            /**
             * return response.
             */

            return response()->json([
                'symbol' => $symbol->with('blockchains')->findOrFail($symbol->id)
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
        $data = $request->except(['picture', 'blockchains']);
        DB::beginTransaction();
        try {
            if ($request->hasFile('picture')) {
                $data['picture'] = $request->file('picture')->store('symbols');
            }

            $symbol = Symbol::create($data);

            /**
             * Handle blockchains.
             */

            if ($request->blockchains) {
                $blockchains = collect($request->blockchains)->mapWithKeys(function ($value) {
                    return [$value['blockchain_id'] => ['transfer_fee' => $value['transfer_fee']]];
                });

                $symbol->blockchains()->sync($blockchains);
            }

            DB::commit();
            /**
             * return response.
             */

            return response()->json([
                'symbol' => $symbol->fresh(),
                'message' => __('messages.symbols.store.successful')
            ], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param  Symbol  $symbol
     * @param  Update  $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function update(Symbol $symbol, Update $request): JsonResponse
    {
        $data = $request->except(['picture', 'blockchains']);
        DB::beginTransaction();
        try {
            if ($request->hasFile('picture')) {
                $data['picture'] = $request->file('picture')->store('symbols');
            }

            $symbol->update($data);

            /**
             * Handle blockchains.
             */

            if ($request->blockchains) {
                $blockchains = collect($request->blockchains)->mapWithKeys(function ($value) {
                    return [$value['blockchain_id'] => ['transfer_fee' => $value['transfer_fee']]];
                });

                $symbol->blockchains()->sync($blockchains);
            }

            DB::commit();
            /**
             * return response.
             */

            return response()->json([
                'symbol' => $symbol->fresh(),
                'message' => __('messages.symbols.update.successful')
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
     * @param Symbol $symbol
     * @return JsonResponse
     * @throws \JsonException
     */

    public function destroy(Symbol $symbol): JsonResponse
    {
        try {
            $symbol->delete();

            /**
             * return response.
             */

            return response()->json([
                'message' => __('messages.symbols.destroy.successful')
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}

<?php

namespace App\Http\Controllers\Admins\Blockchain;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Blockchain\Store;
use App\Http\Requests\Admins\Blockchain\Update;
use App\Models\Blockchain;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BlockchainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:blockchain']);
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
                'blockchains' => Blockchain::with('symbols')->latest()->get()
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Blockchain $blockchain
     * @return JsonResponse
     * @throws \JsonException
     */

    public function show(Blockchain $blockchain): JsonResponse
    {
        try {
            /**
             * return response.
             */

            return response()->json([
                'blockchain' => $blockchain->with('symbols')->findOrFail($blockchain->id)
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
        $data = $request->only([
            'title',
            'name_fa',
            'name_en',
            'deposit_min_needed_confirmations'
        ]);
        DB::beginTransaction();
        try {
            if ($request->hasFile('picture')) {
                $data['picture'] = $request->file('picture')->store('blockchains');
            }

            $blockchain = Blockchain::create($data);

            /**
             * Handle symbols.
             */

            if ($request->symbols) {
                $symbols = collect($request->symbols)->mapWithKeys(function ($value) {
                    return [$value['symbol_id'] => ['transfer_fee' => $value['transfer_fee']]];
                });

                $blockchain->symbols()->sync($symbols);
            }

            DB::commit();
            /**
             * return response.
             */

            return response()->json([
                'message' => __('messages.blockchains.store.successful'),
                'blockchain' => $blockchain->fresh()
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
     * @param  Blockchain  $blockchain
     * @param  Update  $request
     * @return JsonResponse
     * @throws \JsonException
     */

    public function update(Blockchain $blockchain, Update $request): JsonResponse
    {
        $data = $request->only([
            'title',
            'name_fa',
            'name_en',
            'deposit_min_needed_confirmations'
        ]);
        DB::beginTransaction();
        try {
            if ($request->hasFile('picture')) {
                $data['picture'] = $request->file('picture')->store('blockchains');
            }

            $blockchain->update($data);

            /**
             * Handle symbols.
             */

            if ($request->symbols) {
                $symbols = collect($request->symbols)->mapWithKeys(function ($value) {
                    return [$value['symbol_id'] => ['transfer_fee' => $value['transfer_fee']]];
                });

                $blockchain->symbols()->sync($symbols);
            }
            DB::commit();

            /**
             * return response.
             */

            return response()->json([
                'message' => __('messages.blockchains.update.successful'),
                'blockchain' => $blockchain->fresh()
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
     * @param Blockchain $blockchain
     * @return JsonResponse
     * @throws \JsonException
     */

    public function destroy(Blockchain $blockchain): JsonResponse
    {
        try {
            $blockchain->delete();

            /**
             * return response.
             */

            return response()->json([
                'message' => __('messages.blockchains.destroy.successful')
            ]);
        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}

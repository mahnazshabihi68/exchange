<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Users;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user']);

        $this->middleware(['throttle:3,1'])->only(['store']);
    }

    /**
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        return response()->json([
            'documents' => $this->user()->documents()->latest()->get()->map(function ($document) {
                $document->status_casted = __('attributes.documents.status.' . $document->pivot->status);
                return $document;
            }),
            'uploadables' => Document::active()->latest()->get(),
        ]);
    }

    /**
     * @return Authenticatable|null
     */

    public function user(): Authenticatable
    {
        return auth('user')->user();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function store(Request $request): JsonResponse
    {

        /**
         * Validate request.
         */

        $this->validate($request, [
            'document_id' => 'required|numeric|exists:documents,id',
            'document' => 'required|image|max:2048'
        ]);

        /**
         * Fetch document from database where user didnt upload before.
         */

        $document = Document::whereId($request->document_id);

        if (!$document->exists()) {

            return response()->json([
                'error' => __('messages.documents.notFound'),
            ], 404);

        }

        $document = $document->first();

        $status = 1;

        DB::beginTransaction();
        try{
            /**
             * Check if document needs admin approval or what.
             */

            if (!$document->requires_approval) {

                /**
                 * Set isApproved equal to true.
                 */

                $status = 2;

                /**
                 * Make permissions.
                 */

                $permissions = $document->permissions()->get();

                $this->user()->givePermissionTo($permissions);

            }

            /**
             * Store document.
             */

            $data = [
                'document' => $request->file('document')->store('documents'),
                'status' => $status
            ];

            /**
             * Store document in database.
             */

            $this->user()->documents()->attach($request->document_id, $data);

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.documents.store.successful'),
            ]);
        }
        catch(\Exception $exception){
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}

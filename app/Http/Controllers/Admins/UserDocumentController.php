<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Controllers\Admins;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserDocumentController extends Controller
{
    /**
     * @var string
     */

    protected string $table;

    /**
     * UserDocumentController constructor.
     */

    public function __construct()
    {
        $this->table = 'document_user';

        $this->middleware(['auth:admin']);

        $this->middleware('permission:document')->except('requireAttentionCount');
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function requireAttentionCount(): JsonResponse
    {
        try {

            return response()->json([
                'require-attention-count' => DB::table($this->table)->whereStatus(1)->count()
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
     */

    public function index(): JsonResponse
    {
        return response()->json([
            'documents' => DB::table($this->table)->latest()->get()->map(function ($document) {
                $document->user = User::find($document->user_id);
                $document->document = Document::find($document->document_id);
                $document->status_casted = __('attributes.documents.status.' . $document->status);
                return $document;
            }),
        ]);
    }

    /**
     * @param int $document
     * @return JsonResponse
     */

    public function show(int $document): JsonResponse
    {
        $document = DB::table($this->table)->whereId($document);

        if (!$document->exists()) {

            return response()->json([
                'error' => __('messages.documents.notFound'),
            ], 404);

        }

        $document = $document->first();

        $document->status_casted = __('attributes.documents.status.' . $document->status);

        return response()->json([
            'document' => $document
        ]);
    }

    /**
     * @param string $file
     * @return BinaryFileResponse
     */

    public function download(string $file): BinaryFileResponse
    {
        $path = str_replace('_', '/', $file);

        $storage = Storage::disk('storage');

        if (!$storage->exists($path)) {

            abort(405);

        }

        return response()->download(storage_path($path));
    }

    /**
     * @param Request $request
     * @param int $document
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function update(Request $request, int $document): JsonResponse
    {
        /**
         * Validate request.
         */

        $this->validate($request, [
            'is_approved' => 'required|boolean',
            'reject_reason' => 'nullable|required_if:is_approved,false'
        ]);

        $status = $request->is_approved ? 2 : 0;

        $document = DB::table($this->table)->where('id', $document)->where('status', '!=', $status);

        if (!$document->exists()) {

            return response()->json([
                'error' => __('messages.documents.notFound'),
            ], 404);

        }

        $user = User::find($document->first()->user_id);

        $ownerDocument = Document::find($document->first()->document_id);

        DB::beginTransaction();
        try{
            $document->update([
                'status' => $status,
                'reject_reason' => $request->reject_reason
            ]);

            /**
             * Give permissions if document has been approved, or delete the document file..
             */

            if ($request->is_approved) {

                $user->givePermissionTo($ownerDocument->permissions()->get());

            }

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.documents.update.successful')
            ]);
        }
        catch(Exception $exception){
            DB::rollBack();
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}

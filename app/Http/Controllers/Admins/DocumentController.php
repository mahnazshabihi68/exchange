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
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class DocumentController extends Controller
{
    /**
     * DocumentController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:document']);
    }

    /**
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        return response()->json([
            'documents' => Document::with('permissions')->latest()->get()
        ]);
    }

    /**
     * @param Document $document
     * @return JsonResponse
     */

    public function show(Document $document): JsonResponse
    {
        return response()->json([
            'document' => $document->with('permissions')->findOrFail($document->id)
        ]);
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
            'title_fa' => 'required|string|unique:documents',
            'title_en' => 'required|string|unique:documents',
            'requires_approval' => 'required|boolean',
            'permissions' => 'array',
            'description_fa' => 'string',
            'description_en' => 'string',
            'status' => 'required|boolean',
            'example' => 'image|max:1024',
        ]);

        $data = $request->except(['permissions', 'example']);

        DB::beginTransaction();
        try{
            if ($request->hasFile('example')) {

                $data['example'] = $request->file('example')->store('exampleDocuments');

            }

            /**
             * Create document.
             */

            $document = Document::create($data);

            /**
             * Check if document unlocks any permsission or what.
             */

            if ($request->permissions) {

                /**
                 * Fetch permissions.
                 */

                $permissions = Permission::whereIn('id', $request->permissions)->whereGuardName('user')->get();

                /**
                 * Make relationship between document and permissions.
                 */

                $document->permissions()->attach($permissions);

            }
            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.documents.store.successful'),
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

    /**
     * @param Request $request
     * @param Document $document
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function update(Request $request, Document $document): JsonResponse
    {
        /**
         * Validate request.
         */

        $this->validate($request, [
            'title_fa' => 'required|string|unique:documents,title_fa,' . $document->id,
            'title_en' => 'required|string|unique:documents,title_en,' . $document->id,
            'requires_approval' => 'required|boolean',
            'permissions' => 'array',
            'description_fa' => 'string',
            'description_en' => 'string',
            'status' => 'required|boolean',
            'example' => 'image|max:1024',
        ]);

        $data = $request->except(['permissions', 'example']);

        DB::beginTransaction();
        try{
            if ($request->hasFile('example')) {

                $data['example'] = $request->file('example')->store('exampleDocuments');

            }

            /**
             * Update document.
             */

            $document->update($data);

            /**
             * Update permissions.
             */

            $document->permissions()->sync(Permission::whereGuardName('user')->whereIn('id', $request->permissions)->get());

            DB::commit();
            /**
             * Return response.
             */

            return response()->json([
                'message' => __('messages.documents.update.successful'),
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

    /**
     * @param Request $request
     * @param Document $document
     * @return JsonResponse
     * @throws Exception
     */

    public function destroy(Request $request, Document $document): JsonResponse
    {
        /**
         * Check if document has any relation, return cannot be deleted.
         */

        if ($document->permissions()->exists() || $document->users()->exists()) {

            return response()->json([
                'error' => __('messages.documents.destroy.failed'),
            ], 400);

        }

        $document->delete();

        return response()->json([
            'message' => __('messages.documents.destroy.successful'),
        ]);
    }
}

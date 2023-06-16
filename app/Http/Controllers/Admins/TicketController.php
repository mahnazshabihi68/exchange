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
use App\Models\Ticket;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    /**
     * TicketController constructor.
     */

    public function __construct()
    {
        $this->middleware(['auth:admin']);

        $this->middleware(['permission:ticket'])->except('requireAttentionCount');
    }

    /**
     * @return JsonResponse
     * @throws \JsonException
     */

    public function requireAttentionCount(): JsonResponse
    {
        try {

            return response()->json([
                'require-attention-count' => Ticket::whereStatus(1)->count()
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
            'tickets' => Ticket::with('user')->latest()->get()
        ]);
    }

    /**
     * @param Ticket $ticket
     * @return JsonResponse
     */

    public function show(Ticket $ticket): JsonResponse
    {
        $answers = $ticket->answers()->with('answerable')->oldest()->get();

        return response()->json([
            'ticket' => $ticket,
            'answers' => $answers
        ]);
    }

    /**
     * @param Request $request
     * @param Ticket $ticket
     * @return JsonResponse
     * @throws ValidationException
     * @throws \JsonException
     */

    public function answer(Request $request, Ticket $ticket): JsonResponse
    {
        $this->validate($request, [
            'content' => 'required',
            'attachment' => 'image|max:2048'
        ]);

        $data = [
            'content' => nl2br($request->get('content'))
        ];

        DB::beginTransaction();
        try{
            if ($request->has('attachment')) {

                $data['attachment'] = $request->file('attachment')->store('tickets');

            }

            $answer = $this->admin()->answers()->create($data);

            $answer->ticket()->associate($ticket)->save();

            $ticket->update([
                'status' => 2
            ]);

            DB::commit();
            return response()->json([
                'message' => __('messages.tickets.answer.successful')
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
     * @return Authenticatable
     */

    private function admin(): Authenticatable
    {
        return auth('admin')->user();
    }

    /**
     * @param Request $request
     * @param Ticket $ticket
     * @return JsonResponse
     */

    public function close(Ticket $ticket): JsonResponse
    {
        $ticket->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => __('messages.tickets.close.successful')
        ]);
    }

    /**
     * @param Ticket $ticket
     * @return JsonResponse
     * @throws \JsonException
     */

    public function delete(Ticket $ticket): JsonResponse
    {
        try {

            $ticket->delete();

            return response()->json([
                'message'   =>  __('messages.tickets.close.successful')
            ]);

        } catch (Exception $exception){
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
            return response()->json([
                'error' =>  $exception->getMessage()
            ], 400);
        }
    }
}

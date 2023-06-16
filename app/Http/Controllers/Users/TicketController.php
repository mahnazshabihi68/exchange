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
use App\Models\Ticket;
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
        $this->middleware(['auth:user']);
    }

    /**
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        $tickets = $this->user()->tickets()->latest()->get();

        return response()->json([
            'tickets' => $tickets,
        ]);
    }

    /**
     * @return Authenticatable|null
     */

    private function user()
    {
        return auth('user')->user();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'subject' => 'required|string',
            'department' => 'required|string',
            'content' => 'required',
            'attachment' => 'image|max:2048'
        ]);

        $data = [
            'subject' => $request->subject,
            'hash' => $this->tokenGenerator(8, 'tickets', 'hash'),
            'content' => nl2br($request->get('content')),
            'department' => $request->department
        ];

        if ($request->has('attachment')) {

            $data['attachment'] = $request->file('attachment')->store('tickets');
        }

        $this->user()->tickets()->create($data);

        return response()->json([
            'message' => __('messages.tickets.store.successful')
        ]);
    }

    /**
     * @param Ticket $ticket
     * @return JsonResponse
     */

    public function show(Ticket $ticket): JsonResponse
    {
        $answers = $ticket->answers()->oldest()->get();

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
            'content' => nl2br($request->get('content')),
        ];

        if ($request->has('attachment')) {

            $data['attachment'] = $request->file('attachment')->store('tickets');
        }

        DB::beginTransaction();
        try{
            $answer = $this->user()->answers()->create($data);

            $answer->ticket()->associate($ticket)->save();

            $ticket->update([
                'status' => 1
            ]);
            DB::commit();
            return response()->json([
                'message' => __('messages.tickets.answer.successful')
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

    /**
     * @param Request $request
     * @param Ticket $ticket
     * @return JsonResponse
     */

    public function delete(Request $request, Ticket $ticket): JsonResponse
    {
        $ticket->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => __('messages.tickets.close.successful'),
        ]);
    }
}

<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Jobs;

use App\Helpers\Logger;
use App\Helpers\Util;
use App\Mail\SendVerification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $to;

    protected string $subject;

    protected string $body;

    /**
     * SendEmail constructor.
     * @param string $to
     * @param string $subject
     * @param string $body
     */

    public function __construct(string $to, string $subject, string $body)
    {
        $this->to = $to;

        $this->subject = $subject;

        $this->body = $body;

        $this->onQueue('send-email-queue');
    }

    /**
     * @throws \JsonException
     */
    public function handle():void
    {
        try {

            Mail::to($this->to)->send(new SendVerification([
                'subject' => $this->subject,
                'body' => $this->body
            ]));

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), Util::jsonEncodeUnicode($exception->getTrace()));
        }
    }
}

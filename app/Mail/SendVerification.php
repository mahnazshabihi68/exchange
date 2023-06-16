<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVerification extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * SendVerification constructor.
     * @param array $data
     */

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->from(config('mail.from.address'), config('settings.name_' . app()->getLocale()))
            ->view('mails.index')
            ->subject($this->data['subject'])->with([
                'subject' => $this->data['subject'],
                'body' => $this->data['body'],
            ]);
    }
}

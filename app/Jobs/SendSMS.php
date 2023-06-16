<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Jobs;

use App\Classes\SMSIR;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $mobile;

    protected string $templateId;

    protected array $parameters;

    /**
     * SendSMS constructor.
     * @param string $mobile
     * @param int $templateId
     * @param array $parameters
     */

    public function __construct(string $mobile, int $templateId, array $parameters)
    {
        $this->mobile = $mobile;

        $this->templateId = $templateId;

        $this->parameters = $parameters;

        $this->onQueue('send-sms-queue');
    }

    /**
     * @throws GuzzleException
     */

    public function handle()
    {
        $this->SMS()->UltraFastSend([
            'ParameterArray' => $this->parameters,
            'Mobile' => $this->mobile,
            'TemplateId' => $this->templateId,
        ]);
    }

    /**
     * @return SMSIR
     */

    private function SMS(): SMSIR
    {
        return new SMSIR();
    }
}

<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Hermes;

use Exception;
use GuzzleHttp\Client;

class Hermes
{
    /**
     * @var string
     */

    public string $base;

    /**
     * Hermes constructor.
     */

    public function __construct()
    {
        $this->base = config('hermes.url') . '/api/';
    }

    /**
     * @return Client
     * @throws Exception
     */

    protected function client(): Client
    {
        return new Client([
            'base_uri' => $this->base,
            'headers' => [
                'accept' => 'application/json',
            ],
            'http_errors' => false
        ]);
    }
}

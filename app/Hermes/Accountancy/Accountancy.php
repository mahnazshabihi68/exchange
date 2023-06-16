<?php

/******************************************************************************
 * This project is not free and has business trademarks.                      *
 * Ali Khedmati | +989122958172 | Ali@khedmati.ir                             *
 * Copyright (c)  2020-2022, Vorna Co.                                        *
 ******************************************************************************/

namespace App\Hermes\Accountancy;

use App\Hermes\Hermes;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class Accountancy extends Hermes
{
    /**
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */

    public function getAccountancy(): Collection
    {
        return collect(json_decode($this->client()->get('accountancy')->getBody()->getContents())->inequalities);
    }
}

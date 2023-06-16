<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Contracts;

use Illuminate\Support\Collection;

interface BlockchainExplorerContract
{
    /**
     * @return Collection
     */

    public function getTransactions(): Collection;

    /**
     * @return Collection
     */

    public function getBalances(): Collection;

    /**
     * @return bool
     */

    public function addressIsValid(): bool;
}

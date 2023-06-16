<?php

namespace App\Webservices\AtipayWithdraw\interfaces;

use Illuminate\Database\Eloquent\Model;

interface IAtipayWithdrawService
{
    public function directTransfer(array $data);

    public function payaTransfer(array $data);

    public function payaTransferReport(Model $data);

    public function satnaTransfer(array $data);
}

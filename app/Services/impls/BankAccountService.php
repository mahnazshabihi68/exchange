<?php

namespace App\Services\impls;

use App\Repositories\interfaces\IBankAccountRepository;
use App\Services\interfaces\IBankAccountService;

class BankAccountService implements IBankAccountService
{
    private IBankAccountRepository $bankAccountRepository;

    public function __construct(IBankAccountRepository $bankAccountRepository)
    {
        $this->bankAccountRepository = $bankAccountRepository;
    }

    public function getByUserId($userId)
    {
        return $this->bankAccountRepository->getByUserId($userId);
    }

    public function create($data)
    {
        return $this->bankAccountRepository->create($data);
    }

    public function update($data, $userId)
    {
        return $this->bankAccountRepository->update($data, $userId);
    }
}

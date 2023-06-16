<?php

namespace App\Repositories\impls;

use App\Models\bankAccount;
use App\Repositories\interfaces\IBankAccountRepository;

class BankAccountRepository implements IBankAccountRepository
{
    private bankAccount $bankAccount;

    public function __construct(bankAccount $bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    public function getByUserId($userId)
    {
        return $this->bankAccount->where('user_id', $userId)->first();
    }

    public function create($data)
    {
        return $this->bankAccount->create($data);
    }

    public function update($data, $userId): bool
    {
        return $this->bankAccount->where('user_id', $userId)->update($data, $userId);
    }
}

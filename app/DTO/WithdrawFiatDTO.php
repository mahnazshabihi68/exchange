<?php

namespace App\DTO;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\DataTransferObject;

class WithdrawFiatDTO extends DataTransferObject
{
    /**
     * @var float
     */
    #[MapFrom('amount')]
    #[MapTo('amount')]
    public float $amount;

    /**
     * @var string
     */
    #[MapFrom('description')]
    #[MapTo('description')]
    public string $description;

    /**
     * @var string
     */
    #[MapFrom('transferDescription')]
    #[MapTo('transferDescription')]
    public string $transferDescription;

    /**
     * @var string
     */
    #[MapFrom('ibanNumber')]
    #[MapTo('ibanNumber')]
    public string $ibanNumber;

    /**
     * @var string
     */
    #[MapFrom('ownerName')]
    #[MapTo('ownerName')]
    public string $ownerName;

    /**
     * @var string
     */
    #[MapFrom('factorNumber')]
    #[MapTo('factorNumber')]
    public string $factorNumber;

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return WithdrawFiatDTO
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return WithdrawFiatDTO
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransferDescription(): string
    {
        return $this->transferDescription;
    }

    /**
     * @param string $transferDescription
     * @return WithdrawFiatDTO
     */
    public function setTransferDescription(string $transferDescription): self
    {
        $this->transferDescription = $transferDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getIbanNumber(): string
    {
        return $this->ibanNumber;
    }

    /**
     * @param string $ibanNumber
     * @return WithdrawFiatDTO
     */
    public function setIbanNumber(string $ibanNumber): self
    {
        $this->ibanNumber = $ibanNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    /**
     * @param string $ownerName
     * @return WithdrawFiatDTO
     */
    public function setOwnerName(string $ownerName): self
    {
        $this->ownerName = $ownerName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFactorNumber(): string
    {
        return $this->factorNumber;
    }

    /**
     * @param string $factorNumber
     * @return WithdrawFiatDTO
     */
    public function setFactorNumber(string $factorNumber): self
    {
        $this->factorNumber = $factorNumber;
        return $this;
    }
}

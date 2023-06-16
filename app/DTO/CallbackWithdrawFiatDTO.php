<?php

namespace App\DTO;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\DataTransferObject;

class CallbackWithdrawFiatDTO extends DataTransferObject
{
    /**
     * @var int
     */
    #[MapFrom('withdraw_id')]
    #[MapTo('withdraw_id')]
    public int $withdrawId;

    /**
     * @var float
     */
    #[MapFrom('amount')]
    #[MapTo('amount')]
    public float $amount;

    /**
     * @var string
     */
    #[MapFrom('currency')]
    #[MapTo('currency')]
    public string $currency;

    /**
     * @var string
     */
    #[MapFrom('description')]
    #[MapTo('description')]
    public string $description;

    /**
     * @var string
     */
    #[MapFrom('factorNumber')]
    #[MapTo('factorNumber')]
    public string $factorNumber;

    /**
     * @var string
     */
    #[MapFrom('ibanNumber')]
    #[MapTo('ibanNumber')]
    public string $destinationIbanNumber;

    /**
     * @var string
     */
    #[MapFrom('ownerName')]
    #[MapTo('ownerName')]
    public string $ownerName;

    /**
     * @var string
     */
    #[MapFrom('referenceId')]
    #[MapTo('referenceId')]
    public string $referenceId;

    /**
     * @var string
     */
    #[MapFrom('sourceIbanNumber')]
    #[MapTo('sourceIbanNumber')]
    public string $sourceIbanNumber;

    /**
     * @var string
     */
    #[MapFrom('transactionStatus')]
    #[MapTo('transactionStatus')]
    public string $transactionStatus;

    /**
     * @var string
     */
    #[MapFrom('transferDescription')]
    #[MapTo('transferDescription')]
    public string $transferDescription;

    /**
     * @var string
     */
    #[MapFrom('transferStatus')]
    #[MapTo('transferStatus')]
    public string $transferStatus;

    /**
     * @var string
     */
    #[MapFrom('trackerId')]
    #[MapTo('trackerId')]
    public string $trackerId;

    /**
     * @var string|null
     */
    #[MapFrom('transactionId')]
    #[MapTo('transactionId')]
    public ?string $transactionId;

    /**
     * @var bool|null
     */
    #[MapFrom('cancelable')]
    #[MapTo('cancelable')]
    public ?bool $cancelable;

    /**
     * @var bool|null
     */
    #[MapFrom('suspendable')]
    #[MapTo('suspendable')]
    public ?bool $suspendable;

    /**
     * @var bool|null
     */
    #[MapFrom('changeable')]
    #[MapTo('changeable')]
    public ?bool $changeable;

    /**
     * @var bool|null
     */
    #[MapFrom('resumeable')]
    #[MapTo('resumeable')]
    public ?bool $resumeable;

    /**
     * @var string|null
     */
    #[MapFrom('created_at')]
    #[MapTo('created_at')]
    public ?string $createdAt;

    /**
     * @var string|null
     */
    #[MapFrom('updated_at')]
    #[MapTo('updated_at')]
    public ?string $updatedAt;

    /**
     * @return int
     */
    public function getWithdrawId(): int
    {
        return $this->withdrawId;
    }

    /**
     * @param int $withdrawId
     * @return CallbackWithdrawFiatDTO
     */
    public function setWithdrawId(int $withdrawId): self
    {
        $this->withdrawId = $withdrawId;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return CallbackWithdrawFiatDTO
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return CallbackWithdrawFiatDTO
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
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
     * @return CallbackWithdrawFiatDTO
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
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
     * @return CallbackWithdrawFiatDTO
     */
    public function setFactorNumber(string $factorNumber): self
    {
        $this->factorNumber = $factorNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationIbanNumber(): string
    {
        return $this->destinationIbanNumber;
    }

    /**
     * @param string $destinationIbanNumber
     * @return CallbackWithdrawFiatDTO
     */
    public function setDestinationIbanNumber(string $destinationIbanNumber): self
    {
        $this->destinationIbanNumber = $destinationIbanNumber;
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
     * @return CallbackWithdrawFiatDTO
     */
    public function setOwnerName(string $ownerName): self
    {
        $this->ownerName = $ownerName;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceId(): string
    {
        return $this->referenceId;
    }

    /**
     * @param string $referenceId
     * @return CallbackWithdrawFiatDTO
     */
    public function setReferenceId(string $referenceId): self
    {
        $this->referenceId = $referenceId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSourceIbanNumber(): string
    {
        return $this->sourceIbanNumber;
    }

    /**
     * @param string $sourceIbanNumber
     * @return CallbackWithdrawFiatDTO
     */
    public function setSourceIbanNumber(string $sourceIbanNumber): self
    {
        $this->sourceIbanNumber = $sourceIbanNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionStatus(): string
    {
        return $this->transactionStatus;
    }

    /**
     * @param string $transactionStatus
     * @return CallbackWithdrawFiatDTO
     */
    public function setTransactionStatus(string $transactionStatus): self
    {
        $this->transactionStatus = $transactionStatus;
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
     * @return CallbackWithdrawFiatDTO
     */
    public function setTransferDescription(string $transferDescription): self
    {
        $this->transferDescription = $transferDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransferStatus(): string
    {
        return $this->transferStatus;
    }

    /**
     * @param string $transferStatus
     * @return CallbackWithdrawFiatDTO
     */
    public function setTransferStatus(string $transferStatus): self
    {
        $this->transferStatus = $transferStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrackerId(): string
    {
        return $this->trackerId;
    }

    /**
     * @param string $trackerId
     * @return CallbackWithdrawFiatDTO
     */
    public function setTrackerId(string $trackerId): self
    {
        $this->trackerId = $trackerId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    /**
     * @param string|null $transactionId
     * @return CallbackWithdrawFiatDTO
     */
    public function setTransactionId(?string $transactionId): self
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCancelable(): ?bool
    {
        return $this->cancelable;
    }

    /**
     * @param bool|null $cancelable
     * @return CallbackWithdrawFiatDTO
     */
    public function setCancelable(?bool $cancelable): self
    {
        $this->cancelable = $cancelable;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSuspendable(): ?bool
    {
        return $this->suspendable;
    }

    /**
     * @param bool|null $suspendable
     * @return CallbackWithdrawFiatDTO
     */
    public function setSuspendable(?bool $suspendable): self
    {
        $this->suspendable = $suspendable;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getChangeable(): ?bool
    {
        return $this->changeable;
    }

    /**
     * @param bool|null $changeable
     * @return CallbackWithdrawFiatDTO
     */
    public function setChangeable(?bool $changeable): self
    {
        $this->changeable = $changeable;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getResumeable(): ?bool
    {
        return $this->resumeable;
    }

    /**
     * @param bool|null $resumeable
     * @return CallbackWithdrawFiatDTO
     */
    public function setResumeable(?bool $resumeable): self
    {
        $this->resumeable = $resumeable;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @param string|null $createdAt
     * @return CallbackWithdrawFiatDTO
     */
    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @param string|null $updatedAt
     * @return CallbackWithdrawFiatDTO
     */
    public function setUpdatedAt(?string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}

<?php

namespace App\DTO\Log;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\DataTransferObject;

class LogCreationDTO extends DataTransferObject
{
    /**
     * @var string
     */
    #[MapFrom('channel')]
    #[MapTo('channel')]
    public string $channel;

    /**
     * @var string
     */
    #[MapFrom('level')]
    #[MapTo('level')]
    public string $level;

    /**
     * @var string
     */
    #[MapFrom('data')]
    #[MapTo('data')]
    public string $data;

    /**
     * @var string|null
     */
    #[MapFrom('loggable_type')]
    #[MapTo('loggable_type')]
    public ?string $loggableType;

    /**
     * @var string|null
     */
    #[MapFrom('loggable_id')]
    #[MapTo('loggable_id')]
    public ?string $loggableId;

    /**
     * @var int|null
     */
    #[MapFrom('event')]
    #[MapTo('event')]
    public ?int $event;

    /**
     * @var string|null
     */
    #[MapFrom('ip')]
    #[MapTo('ip')]
    public ?string $ip;

    /**
     * @var string|null
     */
    #[MapFrom('device')]
    #[MapTo('device')]
    public ?string $device;

    /**
     * @var string|null
     */
    #[MapFrom('OS')]
    #[MapTo('OS')]
    public ?string $OS;

    /**
     * @var string|null
     */
    #[MapFrom('browser')]
    #[MapTo('browser')]
    public ?string $browser;

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @param  string  $channel
     * @return LogCreationDTO
     */
    public function setChannel(string $channel): self
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @param  string  $level
     * @return LogCreationDTO
     */
    public function setLevel(string $level): self
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param  string  $data
     * @return LogCreationDTO
     */
    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLoggableType(): ?string
    {
        return $this->loggableType;
    }

    /**
     * @param  string|null  $loggableType
     * @return LogCreationDTO
     */
    public function setLoggableType(?string $loggableType): self
    {
        $this->loggableType = $loggableType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLoggableId(): ?string
    {
        return $this->loggableId;
    }

    /**
     * @param  string|null  $loggableId
     * @return LogCreationDTO
     */
    public function setLoggableId(?string $loggableId): self
    {
        $this->loggableId = $loggableId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getEvent(): ?int
    {
        return $this->event;
    }

    /**
     * @param  int|null  $event
     * @return LogCreationDTO
     */
    public function setEvent(?int $event): self
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param  string|null  $ip
     * @return LogCreationDTO
     */
    public function setIp(?string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDevice(): ?string
    {
        return $this->device;
    }

    /**
     * @param  string|null  $device
     * @return LogCreationDTO
     */
    public function setDevice(?string $device): self
    {
        $this->device = $device;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOS(): ?string
    {
        return $this->OS;
    }

    /**
     * @param  string|null  $OS
     * @return LogCreationDTO
     */
    public function setOS(?string $OS): self
    {
        $this->OS = $OS;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    /**
     * @param  string|null  $browser
     * @return LogCreationDTO
     */
    public function setBrowser(?string $browser): self
    {
        $this->browser = $browser;
        return $this;
    }
}

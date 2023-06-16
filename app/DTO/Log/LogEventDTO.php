<?php

namespace App\DTO\Log;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\DataTransferObject;

class LogEventDTO extends DataTransferObject
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
     * @var array
     */
    #[MapFrom('data')]
    #[MapTo('data')]
    public array $data;

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
     * @return LogEventDTO
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
     * @return LogEventDTO
     */
    public function setLevel(string $level): self
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param  array  $data
     * @return LogEventDTO
     */
    public function setData(array $data): self
    {
        $this->data = $data;
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
     * @return LogEventDTO
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
     * @return LogEventDTO
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
     * @return LogEventDTO
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
     * @return LogEventDTO
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
     * @return LogEventDTO
     */
    public function setBrowser(?string $browser): self
    {
        $this->browser = $browser;
        return $this;
    }
}

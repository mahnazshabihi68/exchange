<?php

namespace App\Events\Log;

use App\Enums\Log\LogChannelEnum;
use App\Enums\Log\LogLevelEnum;
use App\Helpers\Logger;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Jenssegers\Agent\Agent;

class BaseLogEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $browser;
    public string $OS;
    public string|array|null $ip;
    public string $device;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public ?string $channel = null,
        public ?string $level = null,
        public ?string $message = null,
        public ?array $data = null
    ) {
        $this->channel = $this->channel ?? LogChannelEnum::APPLICATION->value;
        $this->level = $this->level ?? LogLevelEnum::INFO->value;
        $this->message = $this->message ?? Logger::DEFAULT_LOG_MESSAGE;
        $this->data = $this->data ? array_merge(['message' => $this->message],
            $this->data) : ['message' => $this->message];

        $agent = new Agent();

        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);
        $OS = $agent->platform();
        $OSVersion = $agent->version($OS);

        $this->browser = $browser.' '.$browserVersion;
        $this->OS = $OS.' '.$OSVersion;
        $this->ip = request()?->server('REMOTE_ADDR');
        $this->device = $agent->deviceType();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|PrivateChannel|array
     */
    public function broadcastOn(): Channel|PrivateChannel|array
    {
        return new PrivateChannel('channel-name');
    }
}

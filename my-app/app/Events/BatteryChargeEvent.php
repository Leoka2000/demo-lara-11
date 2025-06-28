<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BatteryChargeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $isCharging;

    public function __construct(bool $isCharging)
    {
        $this->isCharging = $isCharging;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('battery.charging'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'isCharging' => $this->isCharging,
        ];
    }
}

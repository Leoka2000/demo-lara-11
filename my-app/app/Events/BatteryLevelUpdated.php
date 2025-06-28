<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BatteryLevelUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chargeLevel;

    public function __construct(int $chargeLevel)
    {
        $this->chargeLevel = $chargeLevel;
    }


    public function broadcastOn(): array
    {
        return [
            new Channel('battery.chargeLevel'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'chargeLevel' => $this->chargeLevel,
        ];
    }
}

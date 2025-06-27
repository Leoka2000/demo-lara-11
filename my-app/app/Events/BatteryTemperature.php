<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BatteryTemperature implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public int $temperature;

    public function __construct(int $temperature)
    {
        $this->temperature = $temperature;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('battery.temperature'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'temperature' => $this->temperature,
        ];
    }
}

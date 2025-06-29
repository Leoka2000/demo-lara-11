<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class BatteryStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $chargeLevel;
    public float $voltage;
    public int $temperature;

    public function __construct(int $chargeLevel, float $voltage, int $temperature)
    {
        $this->chargeLevel = $chargeLevel;
        $this->voltage = $voltage;
        $this->temperature = $temperature;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('battery.status'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'chargeLevel' => $this->chargeLevel,
            'voltage' => $this->voltage,
            'temperature' => $this->temperature,
        ];
    }
}

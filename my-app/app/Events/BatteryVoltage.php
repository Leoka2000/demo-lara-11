<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BatteryVoltage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public float $voltage;

    /**
     * Create a new event instance.
     */
    public function __construct(float $voltage)
    {
        $this->voltage = $voltage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('battery.voltage'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, float>
     */
    public function broadcastWith(): array
    {
        return [
            'voltage' => $this->voltage,
        ];
    }
}

<?php


namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Log;

class BatteryTemperature implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $temperature;
    public int $timestamp;

    public function __construct(int $temperature, int $timestamp)
    {
        $this->temperature = $temperature;
        $this->timestamp = $timestamp;

        // Log to laravel.log file
        Log::info('Broadcasting BatteryTemperature event', [
            'temperature' => $temperature,
            'timestamp' => $timestamp,
        ]);

        // Print to console if running in CLI
        if (app()->runningInConsole()) {
            fwrite(STDOUT, "Broadcasting BatteryTemperature event: Temp={$temperature}, Timestamp={$timestamp}\n");
        }
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('battery.temperature'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'temperature' => $this->temperature,
            'timestamp' => $this->timestamp,
        ];
    }
}

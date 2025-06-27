<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Events\BatteryTemperature;

class SimulateBatteryTemperature extends Command
{
    protected $signature = 'simulate:battery-temp';
    protected $description = 'Simulate battery temperature increase and send to ThingSpeak and broadcast';

    public function handle()
    {
        $temperature = 25;

        while ($temperature <= 100) {
            // Send to ThingSpeak
            $response = Http::get('https://api.thingspeak.com/update', [
                'api_key' => 'U8OQC9DXGPDTUTTP',
                'field1' => $temperature,
            ]);

            // Broadcast via Reverb
            BatteryTemperature::dispatch($temperature);

            $this->info("Temperature sent: $temperature");

            $temperature += 2;
            sleep(5);
        }

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Events\BatteryVoltage;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class BatteryVoltageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:simulate-voltage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate battery voltage fluctuation and send to ThingSpeak and broadcast';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $voltage = 6.0; // Starting voltage
        $minVoltage = 1.5;
        $decrement = 0.1; // Smaller steps for voltage changes

        while ($voltage >= $minVoltage) {
            // Send to ThingSpeak
            $response = Http::get('https://api.thingspeak.com/update', [
                'api_key' => 'XSFZL9M343SO37JF',
                'field1' => $voltage,
            ]);

            // Broadcast via Reverb
            BatteryVoltage::dispatch($voltage);

            $this->info("Voltage: {$voltage}V");

            $voltage -= $decrement;

            // Add some randomness
            $voltage += (mt_rand(-10, 10) / 100); // Small

            // Ensure voltage stays within bounds
            $voltage = max($minVoltage, min(6.0, $voltage));

            sleep(5); // Same interval as battery charge
        }

        return Command::SUCCESS;
    }
}

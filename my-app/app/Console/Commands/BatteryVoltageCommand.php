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
        $voltage = 12.0;         // Start at full charge
        $minVoltage = 3.5;
        $maxVoltage = 12.0;
        $direction = -1;         // Discharging initially
        $step = 0.1;

        $this->info("Starting voltage simulation...");

        while (true) {
            // Clamp voltage within bounds
            $voltage = max($minVoltage, min($maxVoltage, $voltage));

            // Send to ThingSpeak
            $response = Http::get('https://api.thingspeak.com/update', [
                'api_key' => 'XSFZL9M343SO37JF',
                'field1' => $voltage,
            ]);

            // Broadcast via Reverb
            BatteryVoltage::dispatch($voltage);

            $this->info("Voltage: {$voltage}V");

            // Adjust voltage
            $randomJitter = mt_rand(-5, 5) / 100.0; // ±0.05V jitter
            $voltage += ($step * $direction) + $randomJitter;

            // Switch direction at bounds
            if ($voltage <= $minVoltage) {
                $direction = 1; // start charging
                $this->warn("Voltage low — starting recharge...");
            } elseif ($voltage >= $maxVoltage) {
                $direction = -1; // start discharging
                $this->warn("Voltage high — starting discharge...");
            }

            sleep(5); // simulate delay between updates
        }

        return Command::SUCCESS;
    }
}

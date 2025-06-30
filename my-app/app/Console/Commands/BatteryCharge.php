<?php

namespace App\Console\Commands;

use App\Events\BatteryLevelUpdated;
use Illuminate\Support\Facades\Http;

use Illuminate\Console\Command;

class BatteryCharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:battery-charge';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate battery charge decrease and send to ThingSpeak and broadcast';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chargeLevel = 100;

        while ($chargeLevel >= 1) {
            // Send to ThingSpeak
            $response = Http::get('https://api.thingspeak.com/update', [
                'api_key' => 'K9TL3ZZS2O82HPB7',
                'field1' => $chargeLevel,
            ]);

            // Broadcast via Reverb
            BatteryLevelUpdated::dispatch($chargeLevel);

            $this->info("Charge: $chargeLevel");

            $chargeLevel -= 1;
            sleep(2); // FIX LATER

        }

        return Command::SUCCESS;
    }
}

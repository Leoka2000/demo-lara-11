<?php

namespace App\Http\Controllers;

use App\Events\BatteryLevelUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BatteryController extends Controller
{
    protected $writeApiKey = '2TWNVD8UMRDF8ZL0';
    protected $readApiKey = '4STKZZ4B4CCO7RJJ';
    protected $channelId = '2996889';

    // Update battery level from microcontroller
    public function updateFromDevice(Request $request)
    {
        $level = $request->input('level');

        // Broadcast to frontend via Reverb
        event(new BatteryLevelUpdated($level));

        return response()->json(['success' => true]);
    }

    // Get battery level from ThingSpeak
    public function getBatteryLevel()
    {
        $response = Http::get("https://api.thingspeak.com/channels/{$this->channelId}/fields/1.json", [
            'api_key' => $this->readApiKey,
            'results' => 1
        ]);

        $data = $response->json();
        $latestLevel = $data['feeds'][0]['field1'] ?? 0;

        return response()->json(['level' => $latestLevel]);
    }

    // Send battery level to ThingSpeak
    public function setBatteryLevel(Request $request)
    {
        $level = $request->input('level');

        Http::get("https://api.thingspeak.com/update", [
            'api_key' => $this->writeApiKey,
            'field1' => $level
        ]);

        // Broadcast to frontend
        event(new BatteryLevelUpdated($level));

        return response()->json(['success' => true]);
    }
}

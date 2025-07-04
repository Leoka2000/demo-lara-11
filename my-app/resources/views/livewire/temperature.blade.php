<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;



new class extends Component {


    public function updateTemperature()
    {

    }
};
?>

<div>
    <div class="relative">
        <button id="connectBtn" class=" bg-blue-600 text-xs text-white rounded hover:bg-blue-700">
            Connect to Bluetooth Device
        </button>

        <div class="">
            <div class=" text-xs bg-gray-100 rounded">
                <h3 class="font-bold text-xs">Device Status:</h3>
                <p class="text-xs" id="deviceStatus">Not connected</p>
            </div>

            <div class=" text-xs bg-gray-100 rounded">
                <h3 class=" text-xs font-bold">Temperature Data:</h3>

                <p class="text-xs" id="temperatureData">No data received yet</p>
            </div>

            <div class=" bg-gray-100 text-xs  rounded">
                <h3 class="font-bold text-xs ">WebSocket Log:</h3>
                <div id="wsLog" class=" overflow-y-auto bg-white  rounded text-xs font-mono"></div>
            </div>
        </div>
    </div>
</div>
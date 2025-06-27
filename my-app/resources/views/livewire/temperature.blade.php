<?php

use Livewire\Volt\Component;

use Livewire\Attributes\On;


new class extends Component {
     public int $temperature = 25;

   #[On('echo:battery.temperature,BatteryTemperature')]
    public function updateTemperature($event)
    {
        $this->temperature = $event['temperature'];
    }
}; ?>

<div>
    <h1>Battery Temperature: {{ $temperature }}°C</h1>
</div>

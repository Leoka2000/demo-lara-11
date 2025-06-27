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
    <div>
        <div id="chart-temperature" wire:ignore></div>
        <span id="temp-value">{{ $temperature }}</span>°C</h1>
    </div>
</div>

<script>
    let lastTemp = null;

    const getInitialTemp = () => {
        const el = document.getElementById('temp-value');
        return el ? parseInt(el.textContent) || 0 : 0;
    };

    const chartOptions = {
        chart: {
            height: 210,
            type: 'radialBar',
        },
        series: [getInitialTemp()],
        labels: ['Battery Temp'],
    };

    const chart = new ApexCharts(document.querySelector("#chart-temperature"), chartOptions);
    chart.render();

    setInterval(() => {
        const el = document.getElementById('temp-value');
        if (!el) return;

        const newTemp = parseInt(el.textContent);

        if (newTemp !== lastTemp) {
            chart.updateSeries([newTemp]);
            lastTemp = newTemp;
        }
    }, 200);
</script>

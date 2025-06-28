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
};
?>

<div>
    <div>
        <div id="chart-temperature" wire:ignore></div>
        <span id="temp-value" style="display: none;">{{ $temperature }}</span>
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
            height: 200,
            type: "radialBar"
        },

        series: [getInitialTemp()],

        plotOptions: {
            radialBar: {
                hollow: {
                    margin: 15,
                    size: "70%"
                },
                dataLabels: {
                    showOn: "always",
                    name: {
                        offsetY: -10,
                        show: true,
                        color: "#888",
                        fontSize: "13px"
                    },
                    value: {
                        color: "#111",
                        fontSize: "30px",
                        show: true
                    }
                }
            }
        },

        stroke: {
            lineCap: "round",
        },

        labels: ["Battery Temp"]
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
    }, 100);
</script>

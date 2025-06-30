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
            type: "radialBar",
            toolbar: {
        show: true,
        tools: {
            download: true,
            selection: true,
            zoom: true,
            zoomin: true,
            zoomout: true,
            pan: true,
            reset: true,
        }}

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
                        color: "#4b5563",
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
        fill: {
            type: 'radialGradient',
            colors: ['#f59e0b'] // <-- Set the solid amber color here
        },
        stroke: {
            lineCap: "round",
        },

        labels: ["Temperture °C" ]
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
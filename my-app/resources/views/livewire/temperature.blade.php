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
    <div class="relative p-1">
        <span
            class="  text-xs absolute top-2 left-2 inline-flex items-center px-2.5 py-0.5 rounded-sm d  bg-yellow-100 text-yellow-800 font-medium me-2   dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300">

            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.122 17.645a7.185 7.185 0 0 1-2.656 2.495 7.06 7.06 0 0 1-3.52.853 6.617 6.617 0 0 1-3.306-.718 6.73 6.73 0 0 1-2.54-2.266c-2.672-4.57.287-8.846.887-9.668A4.448 4.448 0 0 0 8.07 6.31 4.49 4.49 0 0 0 7.997 4c1.284.965 6.43 3.258 5.525 10.631 1.496-1.136 2.7-3.046 2.846-6.216 1.43 1.061 3.985 5.462 1.754 9.23Z" />
            </svg>



            2 minutes ago
        </span>

        </svg>


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
<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public int $temperature = 0;
    public string $lastUpdated = 'Never';

    #[On('echo:temperature-updates,TemperatureUpdated')]
    public function updateTemperature($event)
    {
        $this->temperature = $event['temperature'];
        $this->lastUpdated = now()->diffForHumans();

        // Dispatch Livewire event for the chart
        $this->dispatch('battery.temperature', temperature: $this->temperature);
    }
};
?>

<div>
    <div class="relative p-1">
        <span
            class="text-xs absolute top-2 left-2 inline-flex items-center px-2.5 py-0.5 rounded-sm bg-yellow-100 text-yellow-800 font-medium me-2 dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300">
            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.122 17.645a7.185 7.185 0 0 1-2.656 2.495 7.06 7.06 0 0 1-3.52.853 6.617 6.617 0 0 1-3.306-.718 6.73 6.73 0 0 1-2.54-2.266c-2.672-4.57.287-8.846.887-9.668A4.448 4.448 0 0 0 8.07 6.31 4.49 4.49 0 0 0 7.997 4c1.284.965 6.43 3.258 5.525 10.631 1.496-1.136 2.7-3.046 2.846-6.216 1.43 1.061 3.985 5.462 1.754 9.23Z" />
            </svg>
            {{ $lastUpdated }}
        </span>

        <div id="chart-temperature" wire:ignore></div>
        <span id="temp-value" style="display: none;">{{ $temperature }}</span>
    </div>
</div>


<script>
    document.addEventListener('livewire:init', () => {
        let lastTemp = null;
        const chart = initializeChart();

        function initializeChart() {
            const options = {
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
                        }
                    }
                },
                series: [getCurrentTemp()],
                plotOptions: {
                    radialBar: {
                        hollow: { margin: 15, size: "70%" },
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
                                show: true,
                                formatter: (val) => `${val}°C`
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'horizontal',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#f59e0b', '#ef4444'],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100]
                    }
                },
                stroke: { lineCap: "round" },
                labels: ["Temperature °C"]
            };

            return new ApexCharts(document.querySelector("#chart-temperature"), options);
            chart.render();
        }

        function getCurrentTemp() {
            const el = document.getElementById('temp-value');
            return el ? parseInt(el.textContent) || 0 : 0;
        }

        // Listen for Livewire events
        Livewire.on('battery.temperature', (temp) => {
            chart.updateSeries([temp.temperature]);
        });

        // Fallback polling
        setInterval(() => {
            const newTemp = getCurrentTemp();
            if (newTemp !== lastTemp) {
                chart.updateSeries([newTemp]);
                lastTemp = newTemp;
            }
        }, 100);
    });
</script>

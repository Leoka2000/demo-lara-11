<?php

use Livewire\Volt\Component;

new class extends Component {
    //
};
?>

<div wire:ignore>
    <div class="relative p-1">
        <span
            class="text-xs absolute top-2 left-2 inline-flex items-center px-2.5 py-0.5 rounded-sm bg-green-100 text-green-800 font-medium me-2 dark:text-green-400 border border-green-300">
            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 9H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h6m0-6v6m0-6 5.419-3.87A1 1 0 0 1 18 5.942v12.114a1 1 0 0 1-1.581.814L11 15m7 0a3 3 0 0 0 0-6M6 15h3v5H6v-5Z" />
            </svg>
            4 minutes ago
        </span>
        <div id="battery_voltage" class=""></div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chartEl = document.querySelector("#battery_voltage");

        let actualVoltage = 0;
        const maxVoltage = 12;

        const voltageChartOptions = {
            series: [0],
            chart: {
                height: 210,
                type: 'radialBar',
                offsetY: -10,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    track: {
                        background: '#e5e7eb',
                        strokeWidth: '100%',
                        margin: 5
                    },
                    hollow: {
                        size: '70%',
                    },
                    dataLabels: {
                        name: {
                            fontSize: '15px',
                            offsetY: 120
                        },
                        value: {
                            offsetY: 76,
                            fontSize: '15px',
                            formatter: function () {
                                return actualVoltage.toFixed(2) + " Volts";
                            }
                        }
                    }
                }
            },
            tooltip: {
                enabled: true,
                y: {
                    formatter: function (val) {
                        // Convert percent back to voltage
                        const voltage = (val / 100) * maxVoltage;
                        return voltage.toFixed(2) + ' V';
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    shadeIntensity: 0.15,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 65, 91],
                    colorStops: []
                },
                colors: ['#4ade80']
            },
            stroke: {
                dashArray: 4
            },
            labels: ['Battery Voltage']
        };

        const chartVoltage = new ApexCharts(chartEl, voltageChartOptions);
        chartVoltage.render();

        // Listen for voltage updates
        window.Echo.channel('battery.voltage')
            .listen('.App\\Events\\BatteryVoltage', (e) => {
                if (e.voltage !== undefined) {
                    actualVoltage = parseFloat(e.voltage);
                    const percentage = Math.min((actualVoltage / maxVoltage) * 100, 100);
                    const rounded = Math.round(percentage * 100) / 100; // Round to 2 decimal places
                    chartVoltage.updateSeries([rounded]);
                }
            });
    });
</script>
<?php

use Livewire\Volt\Component;

new class extends Component {
    //
};
?>

<div wire:ignore>
    <div class="relative p-1">
        <span
            class="  text-xs absolute top-2 left-2 inline-flex items-center px-2.5 py-0.5 rounded-sm d  bg-green-100 text-green-800 font-medium me-2 dark:text-green-400 border border-green-300">


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



<!-- Alpine for event handling (optional but clean) -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chartEl = document.querySelector("#battery_voltage");

        const voltageChartOptions = {

            series: [23],
            chart: {
                height: 210,
                type: 'radialBar',
                offsetY: -10,
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

            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    dataLabels: {
                        name: {
                            fontSize: '15px',
                            color: undefined,
                            offsetY: 120
                        },
                        value: {
                            offsetY: 76,
                        fontSize: '15px', // DENIFE FONT SIZE caralho
                            color: undefined,
                            formatter: function (val) {
                                return parseFloat(val).toFixed(2) + " Volts";
                            }
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                colors: ['#4ade80'],
                gradient: {
                    shade: 'dark',
                    shadeIntensity: 0.15,
                    inverseColors: false,

                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 65, 91]
                },
            },
            stroke: {
                dashArray: 4
            },
            labels: ['Battery Voltage'],
        };

        const chartVoltage = new ApexCharts(chartEl, voltageChartOptions);
        chartVoltage.render();

        // Reverb listener: channel 'battery.voltage'
        window.Echo.channel('battery.voltage')

            .listen('.App\\Events\\BatteryVoltage', (e) => {
                if (e.voltage !== undefined) {
                    chartVoltage.updateSeries([e.voltage]);
                }
            });
    });
</script>
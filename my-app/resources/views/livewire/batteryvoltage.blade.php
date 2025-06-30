<?php

use Livewire\Volt\Component;

new class extends Component {
    //
};
?>

<div wire:ignore>
    <div id="battery_voltage" class=""></div>
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
                        fontSize: '18px', // DENIFE FONT SIZE caralho
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
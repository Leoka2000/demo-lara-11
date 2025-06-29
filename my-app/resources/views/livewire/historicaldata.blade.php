<?php

use Livewire\Volt\Component;

new class extends Component {
    //
};
?>

<div wire:ignore>
    <div id="historical_data" class="h-[350px]"></div>
</div>



<script>
    document.addEventListener("DOMContentLoaded", function () {
        const historicalDataOptions = {
            series: [{
                name: "Temperature",
                data: [45, 52, 38, 24, 33, 26, 21, 20, 6, 8, 15, 10]
            }, {
                name: "Voltage",
                data: [35, 41, 62, 42, 13, 18, 29, 37, 36, 51, 32, 35]
            }, {
                name: "Baterry charge",
                data: [87, 57, 74, 99, 75, 38, 62, 47, 82, 56, 45, 47]
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: { enabled: false }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: [5, 7, 5],
                curve: 'straight',
                dashArray: [0, 8, 5]
            },
            title: {
                text: 'Historical Data',
                align: 'left'
            },
            legend: {
                tooltipHoverFormatter: function (val, opts) {
                    return val + ' - <strong>' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + '</strong>';
                }
            },
            markers: {
                size: 0,
                hover: {
                    sizeOffset: 6
                }
            },
            xaxis: {
                categories: [
                    '01 Jan', '02 Jan', '03 Jan', '04 Jan', '05 Jan', '06 Jan',
                    '07 Jan', '08 Jan', '09 Jan', '10 Jan', '11 Jan', '12 Jan'
                ]
            },
            tooltip: {
                y: [
                    {
                        title: {
                            formatter: function (val) {
                                return val + " (mins)";
                            }
                        }
                    },
                    {
                        title: {
                            formatter: function (val) {
                                return val + " per session";
                            }
                        }
                    },
                    {
                        title: {
                            formatter: function (val) {
                                return val;
                            }
                        }
                    }
                ]
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };

        const historicalDataChart = new ApexCharts(document.querySelector("#historical_data"), historicalDataOptions);
        historicalDataChart.render();
    });
</script>
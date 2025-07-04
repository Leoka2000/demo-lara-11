<?php

use Livewire\Volt\Component;

new class extends Component {
    //
};
?>


<div wire:ignore>
    <div class="p-2">
        <div id="historical_data"></div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const maxPoints = 20;

        // Example initial temperature data and timestamps (replace with real timestamps)
        const chartData = {
            temperature: [45, 52, 38, 24, 33, 26, 21, 20, 6, 8, 15, 10],
            timestamps: [
                '2025-07-01 10:00', '2025-07-01 10:05', '2025-07-01 10:10', '2025-07-01 10:15',
                '2025-07-01 10:20', '2025-07-01 10:25', '2025-07-01 10:30', '2025-07-01 10:35',
                '2025-07-01 10:40', '2025-07-01 10:45', '2025-07-01 10:50', '2025-07-01 10:55'
            ]
        };

        const historicalDataChart = new ApexCharts(document.querySelector("#historical_data"), {
            series: [
                { name: "Temperature", data: [...chartData.temperature] }
            ],
            chart: {
                type: 'line',
                toolbar: {
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
            dataLabels: { enabled: false },
            stroke: {
                width: 3,
                curve: 'smooth'
            },
            title: {
                text: 'Historical Temperature Data',
                align: 'left',
                style: {
                    color: '#374151',
                    fontSize: '16px',
                    fontWeight: 'thin'
                }
            },
            markers: {
                size: 0,
                hover: { sizeOffset: 6 }
            },
            xaxis: {
                categories: [...chartData.timestamps],
                title: {
                    text: 'Timestamp',
                    style: {
                        color: '#374151',
                        fontWeight: 'bold',
                        fontSize: '14px',
                    }
                },
                labels: {
                    rotate: -45,
                    datetimeUTC: false
                }
            },
            yaxis: {
                title: {
                    text: 'Temperature (°C)',
                    style: {
                        color: '#374151',
                        fontWeight: 'bold',
                        fontSize: '14px',
                    }
                },
                min: 0
            },
            tooltip: {
                y: {
                    formatter: val => val + " °C"
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        });

        historicalDataChart.render();

        function pushAndTrim(array, value) {
            array.push(value);
            if (array.length > maxPoints) array.shift();
            return array;
        }

        // Update chart with live data via Laravel Echo, only temperature updates
        if (typeof Echo !== 'undefined') {
            Echo.channel('battery.temperature')
                .listen('BatteryTemperature', (e) => {
                    chartData.temperature = pushAndTrim(chartData.temperature, e.temperature);

                    // Optionally, update timestamps if available
                    // For now just push dummy timestamp or use e.timestamp if available
                    const now = new Date().toISOString().slice(0, 16).replace('T', ' ');
                    chartData.timestamps = pushAndTrim(chartData.timestamps, now);

                    historicalDataChart.updateOptions({
                        xaxis: {
                            categories: [...chartData.timestamps]
                        }
                    });

                    historicalDataChart.updateSeries([
                        { name: "Temperature", data: chartData.temperature }
                    ]);
                });
        } else {
            console.warn("Laravel Echo is not available.");
        }
    });
</script>
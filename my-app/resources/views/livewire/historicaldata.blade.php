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
    let historicalDataChart;
    const chartData = {
        temperatures: [],
        timestamps: []
    };

    // Initialize the chart
    function initChart() {
        historicalDataChart = new ApexCharts(document.querySelector("#historical_data"), {
            series: [{
                name: "Temperature",
                data: []
            }],
            chart: {
                type: 'line',
                height: 350,
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: ['#3B82F6']
            },
            markers: {
                size: 4
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    formatter: function(value) {
                        return new Date(value).toLocaleTimeString();
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Temperature (°C)'
                },
                min: 0,
                max: 100
            },
            tooltip: {
                x: {
                    format: 'HH:mm:ss'
                },
                y: {
                    formatter: function(val) {
                        return val + " °C";
                    }
                }
            }
        });

        historicalDataChart.render();
    }

    // Initialize the chart
    initChart();

    // Function to update chart with new data
    function updateChart(timestamp, temperature) {
        // Convert timestamp to milliseconds
        const timestampMs = timestamp * 1000;

        // Add new data
        chartData.temperatures.push(temperature);
        chartData.timestamps.push(timestampMs);

        // Maintain max data points
        if (chartData.temperatures.length > maxPoints) {
            chartData.temperatures.shift();
            chartData.timestamps.shift();
        }

        // Prepare series data
        const seriesData = chartData.timestamps.map((ts, index) => {
            return {
                x: ts,
                y: chartData.temperatures[index]
            };
        });

        // Update chart
        historicalDataChart.updateSeries([{
            name: "Temperature",
            data: seriesData
        }]);
    }

    // Initialize Echo
    if (typeof Echo === 'undefined') {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: 'kyvkshzfjpzfeiv0y4et',
            wsHost: window.location.hostname,
            wsPort: 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });
    }

    // Listen for temperature updates
    window.Echo.channel('battery.temperature')
        .listen('BatteryTemperature', (data) => {
            updateChart(data.timestamp, data.temperature);
            console.log('Chart updated with:', data.temperature + '°C at', new Date(data.timestamp * 1000));
        });
});
</script>
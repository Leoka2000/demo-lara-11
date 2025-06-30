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

        const chartData = {
            temperature: [45, 52, 38, 24, 33, 26, 21, 20, 6, 8, 15, 10],
            voltage: [3.5, 4.1, 4.2, 3.8, 3.9, 3.6, 4.0, 3.7, 3.4, 3.9, 3.8, 4.0],
            charge: [87, 57, 74, 99, 75, 38, 62, 47, 82, 56, 45, 47]
        };

        const historicalDataChart = new ApexCharts(document.querySelector("#historical_data"), {
            series: [
                { name: "Temperature", data: [...chartData.temperature] },
                { name: "Voltage", data: [...chartData.voltage] },
                { name: "Battery charge", data: [...chartData.charge] }
            ],
            chart: {

                type: 'line',
                tools: {
            download: true,
            selection: true,
            zoom: true,
            zoomin: true,
            zoomout: true,
            pan: true,
            reset: true,
        },
            },
            dataLabels: { enabled: false },
            stroke: {
                width: [5, 7, 5],
                curve: 'straight',
                dashArray: [0, 8, 5]
            },
            title: {
    text: 'Historical Data',
    align: 'left',
    style: {
        color: '#374151', // <-- set your desired color here
        fontSize: '16px',
        fontWeight: 'thin'
    }
},
            legend: {
                tooltipHoverFormatter: function (val, opts) {
                    return val + ' - <strong>' +
                        opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + '</strong>';
                }
            },
            markers: {
                size: 0,
                hover: { sizeOffset: 6 }
            },
            xaxis: {
                categories: Array.from({ length: chartData.temperature.length }, (_, i) => `T${i + 1}`)
            },
            tooltip: {
                y: [
                    { title: { formatter: val => val + " °C" } },
                    { title: { formatter: val => val + " V" } },
                    { title: { formatter: val => val + "%" } }
                ]
            },
            grid: { borderColor: '#f1f1f1' }
        });

        historicalDataChart.render();


        function pushAndTrim(array, value) {
            array.push(value);
            if (array.length > maxPoints) array.shift();
            return array;
        }

        // === Laravel Echo Listeners ===
        if (typeof Echo !== 'undefined') {
            Echo.channel('battery.chargeLevel')
                .listen('BatteryLevelUpdated', (e) => {
                    chartData.charge = pushAndTrim(chartData.charge, e.chargeLevel);
                    historicalDataChart.updateSeries([
                        { name: "Temperature", data: chartData.temperature },
                        { name: "Voltage", data: chartData.voltage },
                        { name: "Battery charge", data: chartData.charge }
                    ]);
                });

                Echo.channel('battery.voltage')
    .listen('BatteryVoltage', (e) => {
        // Round voltage to 2 decimals before pushing
        const roundedVoltage = parseFloat(parseFloat(e.voltage).toFixed(2));
        chartData.voltage = pushAndTrim(chartData.voltage, roundedVoltage);

        historicalDataChart.updateSeries([
            { name: "Temperature", data: chartData.temperature },
            { name: "Voltage", data: chartData.voltage },
            { name: "Battery charge", data: chartData.charge }
        ]);
    });

            Echo.channel('battery.temperature')
                .listen('BatteryTemperature', (e) => {
                    chartData.temperature = pushAndTrim(chartData.temperature, e.temperature);
                    historicalDataChart.updateSeries([
                        { name: "Temperature", data: chartData.temperature },
                        { name: "Voltage", data: chartData.voltage },
                        { name: "Battery charge", data: chartData.charge }
                    ]);
                });
        } else {
            console.warn("Laravel Echo is not available.");
        }
    });
</script>
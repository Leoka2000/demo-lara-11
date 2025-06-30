<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public int $chargeLevel = 100;

   #[On('echo:battery.chargeLevel,BatteryLevelUpdated')]
public function updateChargeLevel($event)
{
    $this->chargeLevel = $event['chargeLevel'];
}
};
?>

<div>
    <div class="relative p-1">
        <span
            class="  text-xs absolute top-2 left-2 inline-flex items-center px-2.5 py-0.5 rounded-sm d  bg-blue-100 text-blue-800 dark:text-blue-400 border border-blue-400 font-medium me-2   dark:bg-gray-700">

            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                    d="M2.98755 7.97095c0-.55229.44771-1 1-1H16.9253c.5523 0 1 .44771 1 1v7.95855c0 .5522-.4477 1-1 1H3.98755c-.55229 0-1-.4478-1-1V7.97095ZM20.9129 12.9419v-1.9834c0-.5523-.4478-1-1-1h-.9876c-.5523 0-1 .4477-1 1v1.9834c0 .5523.4477 1 1 1h.9876c.5522 0 1-.4477 1-1Z" />
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                    d="M5.9751 9.9585h8.9627v3.9834H5.9751V9.9585Z" />
            </svg>




            2 minutes ago
        </span>
        <div id="chart-battery-percentage" wire:ignore></div>
        <span id="charge-value" style="display: none;">{{ $chargeLevel }}</span>
    </div>
</div>

<script>
    let lastCharge = null;

    const getInitialCharge = () => {
        const el = document.getElementById('charge-value');
        return el ? parseInt(el.textContent) || 0 : 0;
    };

    const batteryChartOptions = {
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
        series: [getInitialCharge()],
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
        stroke: {
            lineCap: "round",
        },
        labels: ["Battery Level"]
    };

    const batteryChart = new ApexCharts(
        document.querySelector("#chart-battery-percentage"),
        batteryChartOptions
    );

    batteryChart.render();

    setInterval(() => {
        const el = document.getElementById('charge-value');
        if (!el) return;

        const newCharge = parseInt(el.textContent);

        if (newCharge !== lastCharge) {
            batteryChart.updateSeries([newCharge]);
            lastCharge = newCharge;
        }
    }, 200);
</script>
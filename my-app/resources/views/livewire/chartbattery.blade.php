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
    <div id="chart-battery-percentage" wire:ignore></div>
    <span id="charge-value" style="display: none;">{{ $chargeLevel }}</span>
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
            type: "radialBar"
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
                        color: "#888",
                        fontSize: "15px"
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

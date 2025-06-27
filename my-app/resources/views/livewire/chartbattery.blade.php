<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <div id=chart_battery class="h-full flex items-center justify-between w-full"></div>
    <script>
        var options = {
  chart: {
    height: 200,
    type: "radialBar"
  },

  series: [72],

  plotOptions: {
    radialBar: {
      hollow: {
        margin: 20,
        size: "60%"
      },

      dataLabels: {
        showOn: "always",
        name: {
          offsetY: -10,
          show: true,
          color: "#888",
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
  labels: ["Batery level"]
};

var chart = new ApexCharts(document.querySelector("#chart_battery"), options);

chart.render();
    </script>
</div>

<script setup>
import { onMounted, ref } from "vue";
import * as echarts from "echarts";

const title = "Dashboard";
// ECharts configuration and rendering
const chart = ref(null);

onMounted(() => {
  const chartInstance = echarts.init(chart.value);

  const options = {
    title: {
      text: "Example Data",
      left: "center",
    },
    tooltip: {
      trigger: "item",
    },
    legend: {
      orient: "horizontal",
      bottom: "0",
    },
    xAxis: {
      type: "category",
      data: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
    },
    yAxis: {
      type: "value",
    },
    series: [
      {
        name: "Scores",
        type: "line",
        data: [100, 90, 80, 80, 90],
        itemStyle: {
          color: "#42A5F5",
        },
        lineStyle: {
          color: "#42A5F5", // Customize the line color
          width: 2,
        },
        itemStyle: {
          color: "#42A5F5", // Color of the points
        },
        smooth: true, // Make the line smooth
      },
    ],
  };

  chartInstance.setOption(options);

  // Resize the chart when the window is resized
  window.addEventListener("resize", chartInstance.resize);
});



// Cleanup on unmount
onUnmounted(() => {
  chartInstance.dispose();
  window.removeEventListener("resize", chartInstance.resize);
});
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="row justify-center">
      <q-card style="width: 100%; max-width: 600px">
        <q-card-section>
          <h3 class="text-center">Basic ECharts Example</h3>
        </q-card-section>
        <q-card-section>
          <!-- Chart Container -->
          <div ref="chart" style="width: 100%; height: 400px"></div>
        </q-card-section>
      </q-card>
    </div>
  </authenticated-layout>
</template>

<style scoped>
/* Optional styling */
</style>

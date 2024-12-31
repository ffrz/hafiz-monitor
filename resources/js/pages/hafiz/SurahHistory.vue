<script setup>
import BtnLink from "@/components/BtnLink.vue";
import { score_to_color, score_to_letter } from "@/helpers/utils";
import { router, usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { Dialog } from "quasar";
import { ref, reactive, onMounted, onUnmounted } from "vue";
import * as echarts from "echarts";

const page = usePage();
const hafiz = ref(page.props.hafiz);
const surah = ref(page.props.surah);
const scores = reactive(page.props.scores);
const title = "Riwayat Hafalan";
const chart = ref(null);
const last_score = scores[Object.keys(scores)[Object.keys(scores).length - 1]].average_score.toFixed(2);

onMounted(() => {
  const xdata = Object.entries(scores).map(([key, value]) =>
    dayjs(value.created_at).format("DD-MM-YY")
  );
  const ydata = Object.entries(scores).map(
    ([key, value]) => value.average_score.toFixed(2)
  );

  const chartInstance = echarts.init(chart.value);

  const options = {
    title: {
      text: "Grafik Hafalan",
      left: "center",
    },
    tooltip: {
      trigger: "axis", // For line charts, "axis" tooltip is better
    },
    legend: {
      orient: "horizontal",
      bottom: "0",
    },
    xAxis: {
      type: "category",
      data: xdata,
      axisLabel: {
        rotate: 90, // Rotate the labels 45 degrees
        interval: 0, // Show all labels
      },
    },
    yAxis: {
      type: "value",
      min: 50, // Minimum value for the y-axis
      max: 100, // Maximum value for the y-axis
      axisLabel: {
        rotate: 45, // Rotate the labels 45 degrees
        interval: 0, // Show all labels
      },
    },
    series: [
      {
        name: "Skor",
        type: "line", // Change this to "line"
        data: ydata,
        lineStyle: {
          color: "#42A5F5", // Customize the line color
          width: 2,
        },
        itemStyle: {
          color: "red", // Color of the points
        },
        smooth: true, // Make the line smooth
      },
    ],
  };

  chartInstance.setOption(options);

  // Resize the chart when the window is resized
  window.addEventListener("resize", chartInstance.resize);

  // Cleanup on unmount
  onUnmounted(() => {
    chartInstance.dispose();
    window.removeEventListener("resize", chartInstance.resize);
  });
});
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="row justify-center">
      <div class="col col-lg-6 q-pa-md">
        <q-card square flat bordered class="full-width">
          <q-card-section>
            <div class="text-subtitle1 text-bold text-grey-9">
              {{ hafiz.name }}
            </div>
            <div class="text-subtitle2 text-bold text-grey-9">
              {{ surah.name }} ({{ surah.total_ayahs }} ayat)
            </div>
            <div>
              Skor Terakhir:
              <span class="text-bold text-grey-9">{{ score_to_letter(last_score) }} / {{ last_score }}</span>
            </div>
            <div>
              Skor Rata-Rata:
              <span class="text-bold text-grey-9">{{ score_to_letter(hafiz.average_score) }} / {{ hafiz.average_score.toFixed(2) }}</span>
            </div>
            <p class="q-my-sm text-caption">
              Riwayat hafalan terakhir (dibatasi sampai 10 hasil penilaian)
            </p>
            <div class="q-my-xl">
              <div ref="chart" style="width: 100%; height: 400px"></div>
            </div>
            <div class="table-container">
              <div class="text-subtitle1 text-grey-9 text-bold text-center">
                Tabel Skor Per Ayat
              </div>
              <table class="table full-width">
                <thead>
                  <tr>
                    <th>No</th>
                    <th
                      v-for="j in Object.keys(scores)"
                      :key="j"
                      :title="scores[j].created_at"
                    >
                      <span class="text-vertical">{{
                        dayjs(scores[j].created_at).format("DD-MM-YY")
                      }}</span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="i in surah.total_ayahs" :key="i">
                    <th>{{ i }}</th>
                    <template v-for="j in Object.keys(scores)">
                      <td>
                        <span
                          class="text-bold"
                          :style="{
                            color: score_to_color(scores[j].details[i]),
                          }"
                        >
                          {{
                            scores[j].details[i]
                              ? score_to_letter(scores[j].details[i])
                              : "-"
                          }}
                        </span>
                      </td>
                    </template>
                  </tr>
                </tbody>
              </table>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>
<style scoped>
.text-vertical {
  writing-mode: vertical-rl;
}
.table {
  border-collapse: collapse;
}

.table tr:nth-child(odd) {
  background-color: #f2f2f2; /* Light gray for odd rows */
}

.table td,
.table th {
  border: 1px solid #333;
  text-align: center;
}

.hafiz-surah-item {
  padding: 5px 0;
  border: 1px solid #eee;
  border-style: solid none none;
}

.hafiz-surah-item:last-child {
  border-style: solid none;
}

.detail-ayah-score {
  border-radius: 5px;
  font-size: 10px;
  width: 24px;
  height: 24px;
  vertical-align: middle;
  line-height: 24px;
  text-align: center;
  color: white;
  border: 1px solid #fff;
}
</style>

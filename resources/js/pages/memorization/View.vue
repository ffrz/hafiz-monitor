<script setup>
import { score_to_letter, score_to_color } from "@/helpers/utils";
import { usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { ref } from "vue";

const page = usePage();
const title = "Hasil Penilaian Hafalan";
const data = ref(page.props.data);
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="row justify-center">
      <div class="col col-lg-6 q-pa-md">
        <q-card
          square
          flat
          bordered
          class="col q-pa-sm full-width full-height-card"
        >
          <q-card-section class="q-pa-sm">
            <div class="text-h6">
              <span>{{ data.hafiz.name }}</span>
            </div>
            <div class="text-subtitle1">
              <span>
                Total Nilai: <span class="text-bold" :style="{color: score_to_color(data.score)}">{{ score_to_letter(data.score) }}
                ({{ data.score.toFixed(2) }})</span>
              </span>
            </div>
            <div class="text-caption">
              <span>{{ data.title }}</span>
              <span>- {{ dayjs(data.created_at).format("DD MMMM YYYY HH:mm") }}</span>
            </div>
          </q-card-section>
          <q-card-section class="q-pa-sm">
            <template v-for="surah in data.details" :key="surah.id">
              <div class="detail-surah">
                {{ surah.id }}: {{ surah.name }}
                (<span :style="{color: score_to_color(surah.score)}">{{ score_to_letter(surah.score) }} / {{ surah.score.toFixed(2) }}</span>)
              </div>
              <div v-for="detail in surah.details" :key="detail.id" class="detail-item">
                <div class="ayah-item">
                  <div class="ayah-number">
                    <span>{{ detail.ayah_number }} : </span>
                    <span
                      :style="{
                        color: score_to_color(detail.score),
                      }"
                      >{{ score_to_letter(detail.score) }}</span
                    >
                  </div>
                  <div class="ayah-text">
                    {{ detail.ayah_text }}
                  </div>
                </div>
                <div v-if="detail.notes" class="text-caption text-grey-8 detail-notes">
                  <q-icon name="note"/>: {{ detail.notes }}
                </div>
              </div>
            </template>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>

<style scoped>
.detail-surah {
  font-weight: bold;
  color: #333;
  background-color: rgb(228, 228, 228);
  padding: 10px 5px;
  border: 1px solid #ddd;
  border-radius: 3px;
  margin: 10px 0;
}

.detail-item {
  border-bottom: 1px dashed #ddd;
}

.ayah-item {
  white-space: normal; /* Allow text to wrap */
  word-wrap: break-word; /* Break long words to avoid overflow */
  overflow-wrap: break-word; /* Prevent overflow if word is too long */
  display: flex;
  padding: 5px;
}

.detail-notes {
  padding: 0 5px;
}

/* Style the Ayah number on the left */
.ayah-number {
  font-weight: bold;
  width: auto; /* Auto width for the first column */
  font-size: 12px;
}

/* Style the Ayah text on the right */
.ayah-text {
  font-size: 20px;
  flex-grow: 1; /* This makes the column grow to take up remaining space */
  margin-left: 20px;
  text-align: right; /* Adjust text alignment as needed for Arabic */
  direction: rtl; /* Right-to-left direction for Arabic text */
  word-wrap: break-word;
  overflow-wrap: break-word;
}
</style>

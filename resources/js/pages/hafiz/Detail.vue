<script setup>
import BtnLink from "@/components/BtnLink.vue";
import { score_to_color, score_to_letter } from "@/helpers/utils";
import { router, usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { Dialog } from "quasar";
import { reactive } from "vue";

const page = usePage();
const data = page.props.data;
const details = reactive(page.props.details);
const title = data.name;

const onDeleteMemorizationClicked = () => {
  Dialog.create({
    title: "Konfirmasi",
    icon: "question",
    message: `Anda yakin akan menghapus semua riwayat hafalan ${data.name}. Setelah dihapus data penilaian hafalan tidak dapat dipulihkan.`,
    focus: "cancel",
    cancel: true,
    persistent: true,
  }).onOk(() => {
    router.post(route("hafiz.clear-score", { id: data.id }));
  });
};

const onDeleteBtnClicked = () => {
  Dialog.create({
    title: "Konfirmasi",
    icon: "question",
    message: `Anda yakin akan menghapus ${data.name}? Semua riwayat hafalan akan terhapus dan tidak dapat dipulihkan.`,
    focus: "cancel",
    cancel: true,
    persistent: true,
  }).onOk(() => {
    router.post(route("hafiz.delete", { id: data.id }));
  });
};
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="row justify-center">
      <div class="col col-lg-6 q-pa-md">
        <q-card square flat bordered class="full-width">
          <q-card-section>
            <div class="flex">
              <div class="col text-subtitle1 text-bold">
                Statistik {{ data.name }}
              </div>
              <BtnLink
                :url="route('hafiz.edit', { id: data.id })"
                icon="edit"
                dense
                flat
                rounded
                class="text-bold"
                title="Edit Profil"
              />
            </div>
            <div class="text-subtitle2">
              Total Nilai:
              <span class="text-bold" v-if="data.average_score"
                >{{ score_to_letter(data.average_score) }} /
                {{ data.average_score.toFixed(2) }}</span
              >
              <span v-else class="text-grey-8">-</span>
            </div>
            <!-- <div class="text-caption">Jumlah Juz Dihafal: {{ data.memorized_juz_count ?? 0 }} Juz</div> -->
            <div class="text-caption">
              Jumlah Surat Dihafal: {{ data.memorized_surah_count ?? 0 }} / 114
              surah
            </div>
            <div class="text-caption">
              Jumlah Ayat Dihafal: {{ data.memorized_ayah_count ?? 0 }} / 6236
              ayat
            </div>
            <template v-if="data.birth_date">
              <div class="text-caption">
                Tanggal Lahir:
                {{ dayjs(data.birth_date).format("DD MMMM YYYY") }} ({{
                  dayjs().diff(data.birth_date, "year")
                }}
                tahun)
              </div>
            </template>
            <div class="text-caption" v-if="data.gender">
              Jenis Kelamin:
              {{ data.gender == "male" ? "Laki-laki" : "Perempuan" }}
            </div>
          </q-card-section>
        </q-card>
        <q-card class="q-my-md full-width" square flat bordered>
          <q-card-section>
            <div class="text-subtitle2 text-bold text-grey-8">
              Hafalan (Penilaian Terakhir)
            </div>
            <div
              class="text-caption q-my-sm"
              v-if="Object.keys(details).length > 0"
            >
              <div class="flex">
                Keterangan Warna:
                <div class="flex q-mr-md q-ml-sm">
                  <div
                    class="detail-ayah-score q-mr-xs"
                    :style="{ 'background-color': score_to_color(100) }"
                  ></div>
                  A [90 - 100]
                </div>
                <div class="flex q-mr-md">
                  <div
                    class="detail-ayah-score q-mr-xs"
                    :style="{ 'background-color': score_to_color(80) }"
                  ></div>
                  B [80 - 89]
                </div>
                <div class="flex">
                  <div
                    class="detail-ayah-score q-mr-xs"
                    :style="{ 'background-color': score_to_color(60) }"
                  ></div>
                  C [< 80]
                </div>
              </div>
            </div>
            <div v-else>
              <div class="text-grey-8 text-italic text-caption">
                Belum ada rekaman
              </div>
            </div>
            <div
              v-for="surah in details"
              :key="surah.surah_id"
              class="hafiz-surah-item"
            >
              <div class="text-bold text-grey-8 text-caption flex" >
                <div class="col">
                  {{ surah.surah_id }}: {{ surah.surah_name }} ({{
                    surah.memorized_ayah_count != surah.ayah_count
                      ? `${surah.memorized_ayah_count}/${surah.ayah_count}`
                      : surah.ayah_count
                  }}
                  ayat :
                  {{
                    (
                      (surah.memorized_ayah_count / surah.ayah_count) *
                      100
                    ).toFixed(0)
                  }}%) -
                  <span :style="{ color: score_to_color(surah.average_score) }"
                    >{{ score_to_letter(surah.average_score) }} /
                    {{ surah.average_score.toFixed(2) }}</span
                  >
                </div>
                <BtnLink dense flat icon="history" rounded :url="route('hafiz.surah-history', { surah_id: surah.surah_id, hafiz_id: data.id })" />
              </div>
              <div class="flex">
                <div
                  v-for="(score, ayah_number) in surah.details"
                  :key="ayah_number"
                >
                  <div
                    class="detail-ayah-score"
                    :style="{ background: score_to_color(score) }"
                  >
                    {{ ayah_number }}
                  </div>
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
        <q-card class="q-my-md full-width" square flat bordered>
          <q-card-section>
            <div class="text-subtitle2 text-bold text-grey-8">Aksi Lainnya</div>
          </q-card-section>
          <q-card-actions>
            <q-btn
              :url="route('hafiz.edit', { id: data.id })"
              icon="delete_forever"
              color="red"
              class="text-bold"
              @click="onDeleteMemorizationClicked"
              >Hapus Riwayat Hafalan</q-btn
            >
            <q-btn
              :url="route('hafiz.edit', { id: data.id })"
              icon="delete_forever"
              color="red"
              class="text-bold"
              @click="onDeleteBtnClicked"
              >Hapus Hafiz</q-btn
            >
          </q-card-actions>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>
<style scoped>
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

<script setup>
import { ref, defineProps, watch, defineEmits } from "vue";

const isVisible = ref(false);
const props = defineProps({
  modelValue: Boolean,
});
watch(
  () => props.modelValue,
  (val) => (isVisible.value = val)
);
const emit = defineEmits(["update:modelValue"]);
</script>

<template>
  <q-dialog v-model="isVisible" @hide="emit('update:modelValue', false)">
    <q-card class="dialog-card q-pa-sm">
      <q-card-section class="sticky-title">
        <div class="text-subtitle1 text-bold text-grey-8 text-center">
          Panduan Skor Penilaian
        </div>
      </q-card-section>
      <q-card-section class="dialog-content">
        <div class="scroll-content">
          <div class="text-subtitle1 text-bold text-grey-9">Penilaian</div>
          <p>Sistem Penilaian dibagi menjadi:</p>
          <ul class="q-ma-none q-pa-none q-pl-md">
            <li class="q-mt-xs">
              <b>Nilai A (Sempurna)</b>
              <br /><b>Kriteria:</b> Hafalan sempurna tanpa kesalahan. <br /><b
                >Indikator:</b
              >
              Lancar tanpa ragu. Pengucapan tajwid dan makharijul huruf benar.
              Tidak ada kesalahan atau lupa dalam hafalan.
            </li>
            <li class="q-mt-xs">
              <b>Nilai B (Baik)</b>
              <br />
              <b>Kriteria:</b>Hafalan sudah bagus tetapi ada sedikit kesalahan
              atau kekurangan kecil.
              <br />
              <b>Indikator: Lancar</b>
              secara umum, tetapi ada keraguan kecil. Tajwid atau makharijul
              huruf kurang sempurna di beberapa bagian. Memerlukan koreksi
              minimal (1-2 kali koreksi per ayat).
            </li>
            <li class="q-mt-xs">
              <b>Nilai C (Cukup)</b>
              <br /><b>Kriteria:</b> Hafalan masih kurang dan memerlukan banyak
              perbaikan. <br /><b>Indikator:</b> Tidak lancar, sering terhenti,
              atau lupa. Kesalahan tajwid atau makharijul huruf yang signifikan.
              Memerlukan koreksi intensif (lebih dari 3 kali koreksi per ayat).
            </li>
          </ul>

          <div class="text-subtitle1 text-bold text-grey-9 q-mt-lg">
            Catatan Setiap Ayat
          </div>
          <p class="q-mt-xs">
            Setiap ayat yang dinilai dapat dilengkapi dengan catatan tambahan
            untuk memberikan informasi detail terkait penilaian. Fitur ini
            dirancang agar pengajar dapat mencatat kesalahan spesifik pada ayat
            tertentu. Saran perbaikan atau latihan tambahan.
            <br />
            Contoh Catatan Ayat:
            <br />QS Al-Ikhlash: "Sempurnakan qalqalah di akhir ayat".
          </p>

          <div class="text-subtitle2 text-bold text-grey-9 q-mt-lg">
            Riwayat Nilai Terakhir Pengujian
          </div>
          <p class="q-mt-xs">
            Aplikasi menyimpan riwayat nilai terakhir di setiap ayat yang yang
            ditampilkan di halaman penilaian. Hal ini bertujuan untuk membantu
            pengajar dalam memantau perkembangan hafalan dari waktu ke waktu
            sehingga mempermudah pengajar dalam memberikan evaluasi secara
            berkelanjutan.
          </p>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<style scoped>
.dialog-card {
  min-width: 300px;
  max-height: 75vh;
  display: flex;
  flex-direction: column;
}

.sticky-title {
  position: sticky;
  top: 0;
  z-index: 1;
  background: white; /* Ensure the background is consistent */
  border-bottom: 1px solid #ddd;
}

.dialog-content {
  overflow-y: auto; /* Enable scrolling */
  flex: 1; /* Allow this section to take up remaining space */
}

.scroll-content {
  padding: 8px;
}
</style>

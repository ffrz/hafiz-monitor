<script setup>
import { usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { ref, onMounted, computed, nextTick } from "vue";
import { useApiForm } from "@/helpers/useApiForm.js";
import { router } from "@inertiajs/vue3";
import { Dialog } from "quasar";
import { score_to_letter, score_to_color } from "@/helpers/utils";
import { getAyahs, getSurahs } from "@/services/quranDatabase";
import { debounce } from "lodash";
import ScoringHelpDialog from "./partials/ScoringHelpDialog.vue";

const page = usePage();
const show_notes_prompt = ref(false);
const show_edit_dialog = ref(false);
const show_help_dialog = ref(false);

const current_notes = ref("");
const current_ayah = ref(null);
const scores = page.props.scores;
const recent_scores = page.props.recent_scores;
const pageTitle = "Penilaian";
const hafizes = page.props.hafizes.map((item) => {
  return { value: item.id, label: item.name };
});

const all_surahs = ref([]);
const surah_options = ref([]);

onMounted(async () => {
  all_surahs.value = await getSurahs();
  surah_options.value = all_surahs.value.map((surah) => {
    return {
      value: surah.id,
      label: `${surah.id} - ${surah.name} (${surah.total_ayahs} ayat)`,
    };
  });
  surah_options.value.reverse();

  nextTick(() => {
    if (surahs.value.length > 0) {
      selectedSurah.value = surahs.value[0];
      handleSurahChanged();
    }
  });
});

const currentHafiz = computed(
  () => page.props.hafizes.find((item) => item.id === form.hafiz_id).name
);

const surahs = computed(() => {
  const surah_ids = [];
  for (let i = form.start_surah_id; i <= form.end_surah_id; i++) {
    const surah = all_surahs.value.find((surah) => surah.id == i);
    if (!surah) break;
    const surahName = surah.name;
    const totalAyahs = surah.total_ayahs;
    surah_ids.push({
      value: i,
      label: `${i} - ${surahName} (${totalAyahs} ayat)`,
    });
  }
  surah_ids.reverse();
  return surah_ids;
});

const form = useApiForm({
  id: parseInt(page.props.data.id),
  hafiz_id: parseInt(page.props.data.hafiz_id),
  title: page.props.data.title ?? "Muraja'ah",
  notes: page.props.data.notes,
  start_surah_id: parseInt(page.props.data.start_surah_id),
  end_surah_id: parseInt(page.props.data.end_surah_id),
  close_session: false,
});

const rows = ref([]);
const selectedSurah = ref(null);

const loading = ref(true);
const pagination = ref({
  page: 1,
  rowsPerPage: null,
  rowsNumber: 10,
  sortBy: "id",
  descending: false,
});

const columns = [
  {
    name: "item",
    label: "Ayat",
    field: "item",
    align: "left",
    sortable: true,
  },
];

const handleSurahChanged = () => {
  // pagination.value.page = 1;
  fetchItems();
};

const fetchItems = async (props = null) => {
  rows.value = await getAyahs(selectedSurah.value.value);
  loading.value = false;
  scrollTo(window, 0, 300);
};

const toggleScore = (row, score) => {
  if (!(row.id in scores)) {
    scores[row.id] = {};
  }

  if (scores[row.id].score == score) {
    delete scores[row.id];
  } else {
    scores[row.id].score = score;
  }

  debounce(function () {
    submitScore();
  }, 2000)();
};

const filteredScores = () => {
  const filteredItems = {};
  for (let ayah_id in scores) {
    if (!ayah_id) continue;
    filteredItems[ayah_id] = scores[ayah_id];
  }
  return filteredItems;
};

const submitScore = async () => {
  const data = form.data();
  const response = await axios.post(
    route("memorization.save", { id: data.id }),
    {
      scores: filteredScores(),
      closeSession: false,
      ...data,
    }
  );
  // todo: show error
  // sementara kita pakai notifikasi ini dulu
  // Notify.create({
  //   message: response.data.message,
  //   position: "top-right",
  //   classes: "custom-notify",
  //   timeout: 1000,
  // });
};

const closeSession = async () => {
  const data = form.data();
  Dialog.create({
    title: "Konfirmasi",
    icon: "question",
    message: "Anda setuju untuk mengakhiri sesi penilaian hafalan?",
    focus: "cancel",
    cancel: true,
    persistent: true,
  }).onOk(() => {
    router.post(route("memorization.save", { id: data.id }), {
      scores: filteredScores(),
      closeSession: true,
      ...data,
    });
  });
};

const addNotes = () => {
  if (!current_ayah.value in scores) {
    scores[current_ayah.value] = {};
  }
  scores[current_ayah.value].notes = current_notes.value;

  current_notes.value = "";
  current_ayah.value = null;

  submitScore();
};

const showNotesDialog = (selectedAyah) => {
  current_ayah.value = selectedAyah;
  current_notes.value = scores[selectedAyah]?.notes;
  show_notes_prompt.value = true;
};

const showEditForm = () => {
  show_edit_dialog.value = true;
};

const onStartSurahChanged = () => {
  generateTitle();
};

const onEndSurahChanged = () => {
  generateTitle();
};

const prevSurah = () => {
  const selectedSurahId = selectedSurah.value.value;
  if (selectedSurahId == surahs.value[0].value) {
    return;
  }
  const currentIndex = surahs.value.findIndex(
    (option) => option.value === selectedSurah.value.value
  );
  selectedSurah.value = surahs.value[currentIndex - 1];
  handleSurahChanged();
};

const nextSurah = () => {
  const selectedSurahId = selectedSurah.value.value;
  if (selectedSurahId == surahs.value[surahs.value.length - 1].value) {
    return;
  }
  const currentIndex = surahs.value.findIndex(
    (option) => option.value === selectedSurah.value.value
  );
  selectedSurah.value = surahs.value[currentIndex + 1];
  handleSurahChanged();
};

const generateTitle = () => {
  let title = "";
  if (form.start_surah_id) {
    title = all_surahs.value[form.start_surah_id - 1].name;
  }

  if (form.end_surah_id && form.end_surah_id != form.start_surah_id) {
    title += " s.d " + all_surahs.value[form.end_surah_id - 1].name;
  }

  form.title = title;
};
</script>

<template>
  <i-head :title="pageTitle" />
  <q-dialog v-model="show_notes_prompt" persistent>
    <q-card style="min-width: 350px" class="q-pa-sm">
      <q-card-section>
        <div class="text-subtitle1 text-grey-9">
          <b>Catatan Ayat</b> (ayat ke-{{
            current_ayah ? rows.find((row) => row.id === current_ayah).number : ''
          }})
        </div>
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-input
          dense
          v-model.trim="current_notes"
          autofocus
          autogrow
          counter
          maxlength="1000"
          clearable
        />
      </q-card-section>
      <q-card-actions align="right" class="text-primary">
        <q-btn label="Batal" v-close-popup icon="cancel" />
        <q-btn
          label="Simpan"
          v-close-popup
          @click="addNotes"
          icon="check"
          color="primary"
          class="text-bold"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>

  <q-dialog v-model="show_edit_dialog" persistent>
    <q-card style="min-width: 350px" class="q-pa-sm">
      <q-card-section>
        <div class="text-subtitle1 text-grey-9">Ubah Info</div>
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-select
          v-model="form.hafiz_id"
          label="Hafidz"
          :options="hafizes"
          map-options
          emit-value
          lazy-rules
          transition-show="jump-up"
          transition-hide="jump-up"
        />
        <q-select
          v-model="form.start_surah_id"
          label="Mulai Surat"
          :options="surah_options"
          map-options
          emit-value
          lazy-rules
          :disable="form.processing"
          @update:model-value="onStartSurahChanged"
          transition-show="jump-up"
          transition-hide="jump-up"
          :error="!!form.errors.start_surah_id"
          :error-message="form.errors.start_surah_id"
        />
        <q-select
          v-if="!!form.start_surah_id"
          v-model="form.end_surah_id"
          label="Sampai Surat"
          :options="surah_options"
          map-options
          emit-value
          lazy-rules
          @update:model-value="onEndSurahChanged"
          :disable="form.processing"
          transition-show="jump-up"
          transition-hide="jump-up"
          :error="!!form.errors.end_surah_id"
          :error-message="form.errors.end_surah_id"
        />
        <q-input
          v-model.trim="form.title"
          label="Judul Sesi"
          clearable
          :disable="form.processing"
          :error="!!form.errors.title"
          :error-message="form.errors.title"
          :rules="[
            (val) => (val && val.length > 0) || 'Judul sesi harus diisi.',
          ]"
        />
        <q-input
          v-if="!!form.id"
          v-model.trim="form.notes"
          type="textarea"
          label="Catatan"
          autogrow
          counter
          maxlength="1000"
          lazy-rules
          :disable="form.processing"
          :error="!!form.errors.notes"
          :error-message="form.errors.notes"
        />
      </q-card-section>
      <q-card-actions align="right" class="text-primary">
        <q-btn label="Batal" v-close-popup icon="cancel" />
        <q-btn
          label="Simpan"
          v-close-popup
          @click="submitScore"
          icon="check"
          color="primary"
          class="text-bold"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>

  <scoring-help-dialog v-model="show_help_dialog" />

  <authenticated-layout>
    <template #left-button v-if="$q.screen.lt.md">
      <q-btn
        icon="arrow_back"
        dense
        flat
        @click="router.get(route('memorization.index'))"
      />
    </template>
    <template #right-button>
      <q-btn
        icon="help"
        color="grey"
        dense
        flat
        rounded
        @click="show_help_dialog = true"
      />
    </template>
    <q-header
      class="bg-grey-1"
      style="top: 49px; border-bottom: 1px solid #eee"
    >
      <q-card-section class="q-pa-sm text-black">
        <div class="col text-subtitle1 text-center">
          <div>
            <span>{{ currentHafiz }}</span>
            <span> - {{ form.title }}</span>
          </div>
          <div class="text-grey-8 text-caption">
            <span>
              {{
                dayjs(page.props.data.created_at).format("DD/MM/YYYY HH:mm")
              }}</span
            >
            <q-btn
              icon="edit"
              class="q-mx-sm"
              style="font-size: 8px"
              flat
              rounded
              dense
              @click="showEditForm"
            />
          </div>
        </div>
        <div class="row flex flex-center align-middle q-gutter-sm items-center">
          <q-btn
            icon="keyboard_arrow_left"
            dense
            rounded
            flat
            @click="prevSurah()"
            :disable="
              selectedSurah &&
              surahs.length &&
              selectedSurah.value === surahs[0].value
            "
          />
          <q-select
            class="col"
            label="Surat"
            v-model="selectedSurah"
            :options="surahs"
            outlined
            @update:model-value="handleSurahChanged"
          />
          <q-btn
            icon="keyboard_arrow_right"
            dense
            flat
            rounded
            @click="nextSurah()"
            :disable="
              selectedSurah &&
              surahs.length &&
              selectedSurah.value === surahs[surahs.length - 1].value
            "
          />
        </div>
      </q-card-section>
    </q-header>
    <template #title>{{ pageTitle }}</template>
    <div class="q-pa-sm mobile-no-padding" style="margin-top: 50px">
      <q-card square flat bordered class="col full-width full-height-card">
        <q-card-section v-show="!!selectedSurah" class="q-pa-none">
          <div
            v-if="
              selectedSurah &&
              selectedSurah.value != 1 &&
              selectedSurah.value != 9
            "
          >
            <p class="basmalah">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
          </div>
          <q-table
            class="q-table-list table-without-pagination"
            style="margin: 0; padding: 0"
            flat
            square
            color="primary"
            row-key="id"
            :loading="loading"
            :columns="columns"
            :rows="rows"
            :hide-header="true"
            :virtual-scroll="true"
            :virtual-scroll-item-size="50"
            :rows-per-page="null"
            :pagination="pagination"
            @request="fetchItems"
          >
            <template v-slot:loading>
              <q-inner-loading showing color="red" />
            </template>
            <template v-slot:body="props">
              <q-tr :props="props">
                <q-td key="item" :props="props">
                  <div>
                    <div class="ayah-item">
                      <span class="ayah-number">{{ props.row.number }}</span>
                      <span class="ayah-text">{{ props.row.text }}</span>
                    </div>
                  </div>
                  <div class="score row q-pa-xs q-gutter-sm">
                    <q-btn
                      dense
                      class="col score-btn"
                      :class="
                        scores[props.row.id]?.score == 100 ? 'text-bold' : ''
                      "
                      :color="
                        scores[props.row.id]?.score == 100 ? 'green' : 'white'
                      "
                      :text-color="
                        scores[props.row.id]?.score == 100 ? 'white' : 'black'
                      "
                      @click="toggleScore(props.row, 100)"
                      label="A"
                    />
                    <q-btn
                      dense
                      class="col score-btn"
                      :class="
                        scores[props.row.id]?.score == 80 ? 'text-bold' : ''
                      "
                      :color="
                        scores[props.row.id]?.score == 80 ? 'orange' : 'white'
                      "
                      :text-color="
                        scores[props.row.id]?.score == 80 ? 'white' : 'black'
                      "
                      @click="toggleScore(props.row, 80)"
                      label="B"
                    />
                    <q-btn
                      dense
                      class="col score-btn"
                      :class="
                        scores[props.row.id]?.score == 60 ? 'text-bold' : ''
                      "
                      :text-color="
                        scores[props.row.id]?.score == 60 ? 'white' : 'black'
                      "
                      :color="
                        scores[props.row.id]?.score == 60 ? 'red' : 'white'
                      "
                      text-color="black"
                      @click="toggleScore(props.row, 60)"
                      label="C"
                    />
                    <q-btn
                      dense
                      class="col score-btn"
                      :icon="
                        !scores[props.row.id]?.score
                          ? 'edit_off'
                          : !scores[props.row.id]?.notes
                          ? 'edit_note'
                          : 'comment'
                      "
                      @click="showNotesDialog(props.row.id)"
                      :text-color="
                        !scores[props.row.id]?.notes ? 'grey' : 'red'
                      "
                      :disabled="!scores[props.row.id]"
                    />
                  </div>
                  <div
                    class="recent-scores q-pa-xs text-grey-8"
                    v-if="recent_scores[props.row.id]"
                  >
                    <q-icon name="history" class="q-mr-sm" /><span
                      v-for="(row, index) in recent_scores[props.row.id]"
                      :key="index"
                      class="q-mr-sm"
                    >
                      <span
                        class="score"
                        :title="row.notes"
                        :style="{
                          color: score_to_color(row.score),
                          'font-weight': 'bold',
                        }"
                        >{{ score_to_letter(row.score) }}
                      </span>
                      <span v-if="row.notes"> : {{ row.notes }} </span>
                    </span>
                  </div>
                </q-td>
              </q-tr>
            </template>
          </q-table>
        </q-card-section>
        <q-footer class="bg-grey-1 q-pa-sm" style="border-top: 1px solid #ddd">
          <div class="full-width">
            <div class="row">
              <q-btn
                icon="check"
                label="SELESAI"
                color="primary"
                class="col q-mt-sm text-bold"
                style="height: 40px"
                @click="closeSession"
              />
            </div>
          </div>
        </q-footer>
      </q-card>
    </div>
  </authenticated-layout>
</template>

<style scoped>
.score-btn:before {
  box-shadow: none !important;
}
.score-btn {
  border: 1px solid #ddd !important;
}
.basmalah {
  direction: rtl;
  text-align: center;
  font-size: 20px;
  border-bottom: 1px dashed #ddd;
  padding-top: none;
  padding: 8px;
}
.recent-scores {
  word-wrap: break-word;
  white-space: normal;
}
.recent-scores .score:before {
  content: "← ";
}

.q-table--horizontal-separator tbody tr:not(:last-child) > td {
  border-style: dashed;
}

.q-table-list .q-td {
  padding: 5px;
}

.ayah-item {
  padding: 10px;
  white-space: normal;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.ayah-item {
  display: flex;
  width: 100%;
  height: 100%;
}

.ayah-number {
  color: #555;
  font-weight: bold;
  width: auto;
  font-size: 12px;
}

.ayah-text {
  font-family: "Lucida Sans", "Lucida Sans Regular", "Lucida Grande",
    "Lucida Sans Unicode", Geneva, Verdana, sans-serif;
  font-weight: normal;
  font-size: 24px;
  flex-grow: 1;
  margin-left: 20px;
  text-align: right;
  direction: rtl;
  word-wrap: break-word;
  overflow-wrap: break-word;
}
.q-table__bottom {
  display: none !important;
}
</style>

<style>
.q-table-list .q-table__bottom .q-table__separator {
  display: none;
}
.q-table-list .q-table__bottom {
  justify-content: center;
}

.custom-notify {
  background-color: #ddd !important;
  border: none !important;
  box-shadow: none !important;
}

.custom-notify .q-notification__message {
  color: #555 !important;
}
</style>

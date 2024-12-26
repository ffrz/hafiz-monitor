<script setup>
import { usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { ref, reactive, computed } from "vue";
import { handleFetchItems } from "@/helpers/client-req-handler";
import { router } from "@inertiajs/vue3";
import { Notify, Dialog } from "quasar";

const page = usePage();
const scores = reactive(page.props.scores);
const title = "Sesi Penilaian Hafalan";
const data = ref(page.props.data);
const surahs = page.props.surahs.map((item) => {
  return {
    value: item.id,
    label: `${item.id} - ${item.name} (${item.total_ayahs} ayat)`,
  };
});
const rows = ref([]);
const selectedSurah = ref(null);

const loading = ref(true);
const pagination = ref({
  page: 1,
  rowsPerPage: 10,
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

const handleSurahChanged = () => fetchItems();

const fetchItems = (props = null) =>
  handleFetchItems({
    pagination,
    props,
    rows,
    loading,
    url: route("ayah.data"),
    filter: { surah_id: selectedSurah.value.value },
  });

const toggleScore = (row, score) => {
  if (!(row.id in scores)) {
    scores[row.id] = {};
  }

  if (scores[row.id].score == score) {
    delete scores[row.id];
    return;
  }

  scores[row.id].score = score;
};

const submitScore = async () => {
  const response = await axios.post(
    route("memorization.save", { id: data.value.id }),
    {
      scores: scores,
      closeSession: false,
    }
  );
  Notify.create({ message: response.data.message });
};

const closeSession = async () => {
  Dialog.create({
    title: "Konfirmasi",
    icon: "question",
    message: "Selesaikan sesi penilaian kali ini?",
    focus: "cancel",
    cancel: true,
    persistent: true,
  }).onOk(() => {
    router.post(route("memorization.save", { id: data.value.id }), {
      scores: scores,
      closeSession: true,
    });
  });
};
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
            <div class="text-subtitle1">
              <span>{{ data.hafiz.name }}</span>
              <span> - {{ data.title }}</span>
              <span>
                -
                {{ dayjs(data.created_at).format("DD MMMM YYYY HH:mm") }}</span
              >
            </div>
            <q-select
              label="Surat"
              v-model="selectedSurah"
              :options="surahs"
              @update:model-value="handleSurahChanged"
            />
          </q-card-section>
          <q-card-section v-show="!!selectedSurah" class="q-pa-sm">
            <q-table
              class="q-table-list"
              style="margin: 0; padding: 0"
              flat
              bordered
              square
              color="primary"
              row-key="id"
              v-model:pagination="pagination"
              :loading="loading"
              :columns="columns"
              :rows="rows"
              :hide-header="true"
              :rows-per-page-options="[10]"
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
                        class="col"
                        :color="
                          scores[props.row.id]?.score === 100 ? 'blue' : 'white'
                        "
                        text-color="black"
                        @click="toggleScore(props.row, 100)"
                        label="A"
                      />
                      <q-btn
                        class="col"
                        :color="
                          scores[props.row.id]?.score === 90 ? 'green' : 'white'
                        "
                        text-color="black"
                        @click="toggleScore(props.row, 90)"
                        label="B"
                      />
                      <q-btn
                        class="col"
                        :color="
                          scores[props.row.id]?.score === 80
                            ? 'yellow'
                            : 'white'
                        "
                        text-color="black"
                        @click="toggleScore(props.row, 80)"
                        label="C"
                      />
                      <q-btn
                        class="col"
                        :color="
                          scores[props.row.id]?.score === 70
                            ? 'orange'
                            : 'white'
                        "
                        text-color="black"
                        @click="toggleScore(props.row, 70)"
                        label="D"
                      />
                      <q-btn
                        class="col"
                        :color="
                          scores[props.row.id]?.score === 60 ? 'red' : 'white'
                        "
                        text-color="black"
                        @click="toggleScore(props.row, 60)"
                        label="E"
                      />
                      <q-btn class="col" label="..." />
                    </div>
                  </q-td>
                </q-tr>
              </template>
            </q-table>
            <div class="full-width q-py-sm">
              <div class="row q-gutter-sm">
                <q-btn
                  label="SIMPAN PENILAIAN"
                  color="grey"
                  class="col q-mt-sm"
                  @click="submitScore"
                />
                <q-btn
                  label="SELESAI"
                  color="primary"
                  class="col q-mt-sm"
                  @click="closeSession"
                />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>

<style scoped>
/* Style the q-table to look like a list */
.q-table-list .q-td {
  padding: 0;
}

.ayah-item {
  padding: 10px;
  white-space: normal; /* Allow text to wrap */
  word-wrap: break-word; /* Break long words to avoid overflow */
  overflow-wrap: break-word; /* Prevent overflow if word is too long */
}

.ayah-item {
  display: flex;
  width: 100%; /* Full width of the container */
  height: 100%; /* Full height of the card */
}

/* Style the Ayah number on the left */
.ayah-number {
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

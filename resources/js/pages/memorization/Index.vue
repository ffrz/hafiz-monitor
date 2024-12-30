<script setup>
import { onMounted, reactive, ref, watch } from "vue";
import { useQuasar } from "quasar";
import { router, usePage } from "@inertiajs/vue3";
import { handleFetchItems, handleDelete } from "@/helpers/client-req-handler";
import dayjs from "dayjs";
import {
  create_options_v2,
  score_to_letter,
  score_to_color,
} from "@/helpers/utils";

const page = usePage();

const hafizes = [
  { value: "all", label: "Semua" },
  ...create_options_v2(page.props.hafizes, "id", "name"),
];

const title = "Penilaian Hafalan";
const $q = useQuasar();
const tableRef = ref(null);
const rows = ref([]);
const loading = ref(true);
const filter = reactive({
  hafiz_id: "all",
  search: "",
});

const pagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 10,
  sortBy: "created_at",
  descending: true,
});

const columns = [
  {
    name: "col_1",
    label: "Rincian",
    field: "col_1",
    align: "left",
  },
  {
    name: "col_2",
    label: "Skor",
    field: "col_2",
    align: "center",
  },
  {
    name: "action",
    label: "",
    align: "right",
  },
];

onMounted(() => {
  const savedFilter = localStorage.getItem("hafiz-monitor.memorization.filter");
  if (savedFilter) {
    // ini akan mentrigger fetchItems
    Object.assign(filter, JSON.parse(savedFilter));
    // return; // kadang mengakibatkan gagal fetch
  }
  fetchItems();
});

watch(
  filter,
  (newValue) => {
    localStorage.setItem(
      "hafiz-monitor.memorization.filter",
      JSON.stringify(newValue)
    );
  },
  { deep: true }
);

const onFilterChange = () => fetchItems();

const fetchItems = (props = null) =>
  handleFetchItems({
    pagination,
    props,
    rows,
    loading,
    filter,
    url: route("memorization.data"),
  });

const deleteItem = (row) =>
  handleDelete({
    url: route("memorization.delete", { id: row.id }),
    message:
      `Semua penilaian hafalan pada sesi ini akan dihapus. ` +
      (!!row.score ? "Hapus" : "Batalkan") +
      ` sesi ${row.title} - ${row.hafiz.name}?`,
    fetchItemsCallback: fetchItems,
    loading,
  });
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="q-pa-md">
      <q-table
        ref="tableRef"
        flat
        bordered
        square
        :dense="true || $q.screen.lt.md"
        color="primary"
        row-key="id"
        virtual-scroll
        title="Hafidz"
        v-model:pagination="pagination"
        :filter="filter.search"
        :loading="loading"
        :columns="columns"
        :rows="rows"
        :rows-per-page-options="[10, 25, 50]"
        @request="fetchItems"
        binary-state-sort
      >
        <template v-slot:loading>
          <q-inner-loading showing color="red" />
        </template>

        <template #top>
          <div class="col">
            <div class="row q-mt-xs q-mb-md q-col-gutter-xs items-center">
              <div class="col-auto">
                <q-btn
                  color="primary"
                  icon="play_arrow"
                  @click="router.get(route('memorization.create'))"
                  label="Penilaian Baru"
                >
                  <q-tooltip>Penilaian Baru</q-tooltip>
                </q-btn>
              </div>
              <q-space class="col-auto" />
              <q-select
                v-model="filter.hafiz_id"
                class="custom-select col-12 col-sm-2"
                :options="hafizes"
                label="Hafidz"
                dense
                map-options
                emit-value
                outlined
                flat
                style="min-width: 150px"
                @update:model-value="onFilterChange"
              />
              <q-input
                class="col-12 col-sm-2"
                dense
                debounce="300"
                v-model="filter.search"
                placeholder="Cari"
                clearable
                outlined
              >
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </div>
          </div>
        </template>

        <template v-slot:no-data="{ icon, message, term }">
          <div class="full-width row flex-center text-grey-8 q-gutter-sm">
            <span>{{ message }} {{ term ? " with term " + term : "" }}</span>
          </div>
        </template>

        <template v-slot:body="props">
          <q-tr
            :props="props"
            :class="props.row.status == 'open' ? 'bg-orange-1' : ''"
          >
            <q-td key="col_1" :props="props">
              <div>
                <div class="text-bold">{{ props.row.hafiz.name }}</div>
                <div>
                  <div class="text-grey-9">{{ props.row.title }}</div>
                  <div class="text-grey-8 text-caption"><q-icon name="schedule" /> {{ dayjs(props.row.created_at).format("DD-MM-YYYY HH:mm") }}</div>
                </div>
              </div>
            </q-td>
            <q-td key="col_2" :props="props">
              <span v-if="props.row.status == 'open'">
                Berlangsung
              </span>
              <span v-else class="text-bold" :style="{ color: score_to_color(props.row.score) }">
                {{ score_to_letter(props.row.score) }} /
                {{ props.row.score.toFixed(2) }}
              </span>
            </q-td>
            <q-td
              key="action"
              class="q-gutter-x-sm"
              :props="props"
              align="center"
              style="color: #555"
            >
              <q-btn
                v-if="props.row.status == 'closed'"
                rounded
                dense
                flat
                @click="
                  router.get(route('memorization.view', { id: props.row.id }))
                "
                icon="visibility"
              >
                <q-tooltip>Lihat</q-tooltip>
              </q-btn>
              <q-btn
                rounded
                dense
                flat
                @click="
                  router.get(route('memorization.run', { id: props.row.id }))
                "
                :icon="props.row.status == 'open' ? 'play_circle' : 'edit'"
              >
                <q-tooltip>{{
                  props.row.status == "open" ? "Lanjutkan Sesi" : "Edit Sesi"
                }}</q-tooltip>
              </q-btn>
              <q-btn
                rounded
                dense
                flat
                :icon="props.row.status == 'open' ? 'cancel' : 'delete_forever'"
                @click="deleteItem(props.row)"
              >
                <q-tooltip>{{
                  props.row.status == "open" ? "Batalkan Sesi" : "Hapus Sesi"
                }}</q-tooltip>
              </q-btn>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </authenticated-layout>
</template>

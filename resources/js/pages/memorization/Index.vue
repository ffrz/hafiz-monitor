<script setup>
import { onMounted, reactive, ref, watch } from "vue";
import { useQuasar } from "quasar";
import { router, usePage } from "@inertiajs/vue3";
import { handleFetchItems, handleDelete } from "@/helpers/client-req-handler";
import dayjs from "dayjs";
import { create_options_v2 } from "@/helpers/utils";

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
  hafiz: "all",
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
    name: "created_at",
    label: "Waktu",
    field: "created_at",
    align: "left",
    sortable: true,
  },
  {
    name: "title",
    label: "Judul Sesi",
    field: "title",
    align: "left",
    sortable: true,
  },
  {
    name: "hafiz",
    label: "Hafidz",
    field: "hafiz",
    align: "left",
    sortable: true,
  },
  {
    name: "score",
    label: "Nilai",
    field: "score",
    align: "left",
    sortable: true,
  },
  {
    name: "status",
    label: "Status",
    field: "status",
    align: "left",
    sortable: true,
  },
  {
    name: "action",
    label: "Aksi",
    align: "center",
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
                  label="Sesi Baru"
                >
                  <q-tooltip>Sesi Baru</q-tooltip>
                </q-btn>
              </div>
              <q-space class="col-auto" />
              <q-select
                v-model="filter.hafiz"
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
            <q-td key="created_at" :props="props">
              {{ dayjs(props.row.created_at).format("DD-MM-YYYY HH:mm") }}
            </q-td>
            <q-td key="title" :props="props">
              {{ props.row.title }}
            </q-td>
            <q-td key="hafiz" :props="props">
              {{ props.row.hafiz.name }}
            </q-td>
            <q-td key="score" :props="props">
              {{
                props.row.status == "open" ? "-" : Math.round(props.row.score)
              }}
            </q-td>
            <q-td key="status" :props="props">
              {{ props.row.status == "open" ? "Sedang Berjalan" : "Selesai" }}
            </q-td>
            <q-td
              key="action"
              class="q-gutter-x-sm"
              :props="props"
              align="center"
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

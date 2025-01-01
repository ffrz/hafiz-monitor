<script setup>
import { onMounted, reactive, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { handleFetchItems } from "@/helpers/client-req-handler";

const statuses = [
  { value: "all", label: "Semua" },
  { value: "active", label: "Aktif" },
  { value: "inactive", label: "Tidak Aktif" },
];

const title = "Hafidz";
const tableRef = ref(null);
const rows = ref([]);
const loading = ref(true);
const filter = reactive({
  status: "all",
  search: "",
});

const pagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 10,
  sortBy: "name",
  descending: false,
});

const columns = [
  {
    name: "name",
    label: "Nama",
    field: "name",
    align: "left",
    sortable: true,
  }
];

onMounted(() => {
  const savedFilter = localStorage.getItem("hafiz-monitor.hafiz.filter");
  if (savedFilter) {
    Object.assign(filter, JSON.parse(savedFilter));
  }
  fetchItems();
});

watch(
  filter,
  (newValue) => {
    localStorage.setItem(
      "hafiz-monitor.hafiz.filter",
      JSON.stringify(newValue)
    );
  },
  { deep: true }
);

const onFilterChange = () => fetchItems();

const fetchItems = (props = null) => {
  handleFetchItems({
    pagination,
    props,
    rows,
    loading,
    filter,
    url: route("hafiz.data"),
  });
}

const onRowClick = (row) => {
  router.get(route('hafiz.detail', row.id));
}

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
                  icon="add"
                  @click="router.get(route('hafiz.add'))"
                  label="Baru"
                >
                  <q-tooltip>Hafiz Baru</q-tooltip>
                </q-btn>
              </div>
              <q-space class="col-auto" />
              <q-select
                v-model="filter.status"
                class="custom-select col-12 col-sm-2"
                :options="statuses"
                label="Status"
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
          <q-tr :props="props" :class="!props.row.active ? 'bg-red-1' : ''"  @click="onRowClick(props.row)" class="cursor-pointer">
            <q-td key="name" :props="props">
              {{ props.row.name }}
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </authenticated-layout>
</template>

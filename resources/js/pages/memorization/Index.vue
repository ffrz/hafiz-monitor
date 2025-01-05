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

const title = "Penilaian";
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

const onRowClicked = (row) => {
  if (row.status == "closed") {
    router.get(route("memorization.view", { id: row.id }));
  } else {
    router.get(route("memorization.run", { id: row.id }));
  }
};
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #right-button>
      <q-btn
        icon="add"
        dense
        color="primary"
        @click="router.get(route('memorization.create'))"
      />
    </template>
    <q-header
      style="
        top: 49px;
        background: #fff;
        border-bottom: 1px solid #ddd;
        border-top: 1px solid #ddd;
      "
    >
      <div class="row q-col-gutter-xs items-center q-pa-sm">
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
    </q-header>
    <template #title>{{ title }}</template>
    <div class="q-pa-md mobile-no-padding" style="margin-top: 50px">
      <q-table
        ref="tableRef"
        flat
        bordered
        square
        :dense="true || $q.screen.lt.md"
        color="primary"
        row-key="id"
        virtual-scroll
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

        <template v-slot:no-data="{ icon, message, term }">
          <div class="full-width row flex-center text-grey-8 q-gutter-sm">
            <span>{{ message }} {{ term ? " with term " + term : "" }}</span>
          </div>
        </template>

        <template v-slot:body="props">
          <q-tr
            @click="onRowClicked(props.row)"
            class="cursor-pointer"
            :props="props"
            :class="props.row.status == 'open' ? 'bg-orange-1' : ''"
          >
            <q-td key="col_1" :props="props">
              <div>
                <div class="text-bold">{{ props.row.hafiz.name }}</div>
                <div>
                  <span v-if="props.row.status == 'open'"
                    >( Sedang Dinilai )</span
                  >
                  <span
                    v-else
                    class="text-bold text-subtitle2"
                    :style="{ color: score_to_color(props.row.score) }"
                  >
                    {{ score_to_letter(props.row.score) }} /
                    {{ props.row.score.toFixed(2) }}
                  </span>
                </div>
                <div>
                  <div class="text-grey-9">{{ props.row.title }}</div>
                  <div class="text-grey-8 text-caption flex items-center">
                    <q-icon name="schedule" />
                    <span class="q-ml-sm">{{
                      dayjs(props.row.created_at).format("DD-MM-YYYY HH:mm")
                    }}</span>
                  </div>
                </div>
              </div>
            </q-td>
            <q-td
              key="action"
              class="q-gutter-x-sm"
              :props="props"
              align="right"
              style="color: #555"
            >
              <div class="full-width">
                <q-btn
                  icon="more_vert"
                  dense
                  flat
                  style="height: 40px; width: 30px"
                  @click.stop
                >
                  <q-menu
                    anchor="bottom right"
                    self="top right"
                    transition-show="flip-right"
                    transition-hide="flip-left"
                  >
                    <q-list style="width: 200px">
                      <q-item
                        clickable
                        v-ripple
                        v-close-popup
                        v-if="props.row.status == 'closed'"
                        @click.stop="
                          router.get(
                            route('memorization.run', { id: props.row.id })
                          )
                        "
                      >
                        <q-item-section avatar>
                          <q-icon name="edit" />
                        </q-item-section>
                        <q-item-section icon="edit"> Edit </q-item-section>
                      </q-item>
                      <q-item
                        @click.stop="deleteItem(props.row)"
                        clickable
                        v-ripple
                      >
                        <q-item-section avatar>
                          <q-icon
                            :name="
                              props.row.status == 'open'
                                ? 'cancel'
                                : 'delete_forever'
                            "
                          />
                        </q-item-section>
                        <q-item-section>
                          {{
                            props.row.status == "open" ? "Batalkan" : "Hapus"
                          }}
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </q-btn>
              </div>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </authenticated-layout>
</template>

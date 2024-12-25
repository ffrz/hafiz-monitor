<script setup>
import { onMounted, reactive, ref, watch } from "vue";
import { useQuasar } from "quasar";
import { router, usePage } from "@inertiajs/vue3";
import { handleFetchItems, handleDelete } from "@/helpers/client-req-handler";
import { create_options } from "@/helpers/utils";

const roles = [
  { value: "all", label: "Semua" },
  ...create_options(window.CONSTANTS.USER_ROLES),
];
const statuses = [
  { value: "all", label: "Semua" },
  { value: "active", label: "Aktif" },
  { value: "inactive", label: "Tidak Aktif" },
];

const page = usePage();
const currentUser = page.props.auth.user;
const title = "Pengguna";
const $q = useQuasar();
const tableRef = ref(null);
const rows = ref([]);
const loading = ref(true);
const filter = reactive({
  role: "all",
  status: "all",
  search: "",
});

const pagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 10,
  sortBy: "username",
  descending: false,
});

const columns = [
  {
    name: "username",
    label: "Username",
    field: "username",
    align: "left",
    sortable: true,
  },
  {
    name: "name",
    label: "Nama",
    field: "name",
    align: "left",
    sortable: true,
  },
  {
    name: "email",
    label: "Email",
    field: "email",
    align: "left",
    sortable: true,
  },
  {
    name: "role",
    label: "Hak Akses",
    field: "role",
    align: "center",
    sortable: true,
  },
  {
    name: "action",
    label: "Aksi",
    align: "center",
  },
];

onMounted(() => {
  const savedFilter = localStorage.getItem("fixsync.users.filter");
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
    localStorage.setItem("fixsync.users.filter", JSON.stringify(newValue));
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
    url: route("admin.user.data"),
  });

const deleteItem = (row) =>
  handleDelete({
    url: route("admin.user.delete", row.id),
    title: `Hapus pengguna ${row.name}?`,
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
        title="Pengguna"
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
                  @click="router.get(route('admin.user.add'))"
                  label="Baru"
                >
                  <q-tooltip>Pengguna Baru</q-tooltip>
                </q-btn>
              </div>
              <q-space v-show="$q.screen.gt.xs" />
              <q-select
                class="custom-select col-12 col-sm-2"
                v-model="filter.role"
                :options="roles"
                label="Role"
                dense
                map-options
                emit-value
                outlined
                @update:model-value="onFilterChange"
              />
              <q-select
                class="custom-select col-12 col-sm-2"
                v-model="filter.status"
                :options="statuses"
                label="Status"
                dense
                map-options
                emit-value
                outlined
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
          <q-tr :props="props" :class="!props.row.active ? 'bg-red-1' : ''">
            <q-td key="username" :props="props">
              {{ props.row.username }}
            </q-td>
            <q-td key="name" :props="props">
              {{ props.row.name }}
            </q-td>
            <q-td key="email" :props="props">
              {{ props.row.email }}
            </q-td>
            <q-td key="role" :props="props" align="center">
              <span>{{ $CONSTANTS.USER_ROLES[props.row.role] }}</span>
            </q-td>
            <q-td
              key="action"
              class="q-gutter-x-sm"
              :props="props"
              align="center"
            >
              <q-btn
                :disable="
                  props.row.id == currentUser.id ||
                  props.row.email == 'admin@example.com'
                "
                rounded
                dense
                flat
                icon="edit"
                @click="router.get(route('admin.user.edit', props.row.id))"
              >
                <q-tooltip>Edit Pengguna</q-tooltip>
              </q-btn>
              <q-btn
                :disable="
                  props.row.id == currentUser.id ||
                  props.row.email == 'admin@example.com'
                "
                rounded
                dense
                flat
                icon="delete"
                @click="deleteItem(props.row)"
              >
                <q-tooltip>Hapus Pengguna</q-tooltip>
              </q-btn>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </authenticated-layout>
</template>

<script setup>
import { handleSubmit } from "@/helpers/client-req-handler";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { getSurahs } from "@/services/quranDatabase";
import { onMounted, ref } from "vue";

const page = usePage();
const title = "Mulai Sesi Penilaian Hafalan";
const hafizes = page.props.hafizes.map((item) => {
  return { value: item.id, label: item.name };
});

let db_surahs = [];
const surahs = ref([]);

onMounted(async () => {
  db_surahs = await getSurahs();
  surahs.value = db_surahs.map((surah) => {
    return {
      value: surah.id,
      label: `${surah.id} - ${surah.name} (${surah.total_ayahs} ayat)`,
    };
  });
});

const form = useForm({
  id: page.props.data.id,
  hafiz_id: page.props.data.hafiz_id,
  title: page.props.data.title ?? "",
  notes: page.props.data.notes,
  start_surah_id: page.props.data.start_surah_id,
  end_surah_id: page.props.data.end_surah_id,
});

const submit = () => handleSubmit({ form, url: route("memorization.create") });

const onStartSurahChanged = () => {
  form.end_surah_id = form.start_surah_id;
  generateTitle();
};

const onEndSurahChanged = () => {
  generateTitle();
};

const generateTitle = () => {
  let title = "";
  if (form.start_surah_id) {
    title = db_surahs[form.start_surah_id - 1].name;
  }

  if (form.end_surah_id && form.end_surah_id != form.start_surah_id) {
    title += " s.d " + db_surahs[form.end_surah_id - 1].name;
  }

  form.title = title;
};
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="row justify-center">
      <div class="col col-lg-6 q-pa-md">
        <q-form class="row" @submit.prevent="submit">
          <q-card square flat bordered class="col q-pa-sm">
            <q-card-section class="q-pt-none">
              <input type="hidden" name="id" v-model="form.id" />
              <q-select
                autofocus
                v-model="form.hafiz_id"
                label="Hafidz"
                :options="hafizes"
                map-options
                emit-value
                lazy-rules
                :disable="form.processing"
                transition-show="jump-up"
                transition-hide="jump-up"
                :error="!!form.errors.hafiz_id"
                :error-message="form.errors.hafiz_id"
              />
              <q-select
                v-model="form.start_surah_id"
                label="Mulai Surat"
                :options="surahs"
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
                :options="surahs"
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
                lazy-rules
                :error="!!form.errors.title"
                :disable="form.processing"
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
            <q-card-actions>
              <q-btn
                icon="play_arrow"
                type="submit"
                label="Mulai"
                color="primary"
                :disable="form.processing"
                @click="submit"
              />
              <q-btn
                icon="cancel"
                label="Batal"
                class="text-black"
                :disable="form.processing"
                @click="router.get(route('memorization.index'))"
              />
            </q-card-actions>
          </q-card>
        </q-form>
      </div>
    </div>
  </authenticated-layout>
</template>

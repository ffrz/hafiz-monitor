<script setup>
import { handleSubmit } from "@/helpers/client-req-handler";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { create_options_v2 } from "@/helpers/utils";
import DatePicker from "@/components/DatePicker.vue";


const page = usePage();
const title = "Mulai Sesi Penilaian Hafalan";
const hafizes = page.props.hafizes.map((item) => {
    return { 'value': item.id, 'label': item.name };
});

const form = useForm({
  id: page.props.data.id,
  hafiz_id: page.props.data.hafiz_id,
  title: page.props.data.title,
  notes: page.props.data.notes,
});

const submit = () => handleSubmit({ form, url: route("memorization.create") });
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
              <q-input v-if="!!form.id"
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

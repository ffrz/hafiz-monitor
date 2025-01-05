<script setup>
import UpdatePasswordForm from './partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './partials/UpdateProfileInformationForm.vue';
import { router } from "@inertiajs/vue3";
const title = 'Profil Saya';
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #left-button>
      <q-btn icon="arrow_back" dense flat @click="router.get(route('memorization.index'))"/>
    </template>
    <template #title>{{ title }}</template>
    <div class="row justify-center">
      <div class="col col-lg-6 q-py-md q-px-sm q-gutter-md">
        <UpdateProfileInformationForm />
        <UpdatePasswordForm />
      </div>
    </div>
  </authenticated-layout>
</template>

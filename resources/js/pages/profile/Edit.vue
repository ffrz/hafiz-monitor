<script setup>
import UpdatePasswordForm from './partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './partials/UpdateProfileInformationForm.vue';
import { router } from "@inertiajs/vue3";
const title = 'Profil Saya';
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #left-button v-if="$q.screen.lt.md">
      <q-btn icon="arrow_back" dense flat @click="router.get(route('memorization.index'))"/>
    </template>
    <template #title>{{ title }}</template>
    <template #right-button v-if="$q.screen.lt.md">
      <q-btn icon="logout" color="grey" label="Logout" dense  @click="router.post(route('logout'))"/>
    </template>
    <div class="row justify-center q-pa-sm">
      <div class="col col-md-6">
        <UpdateProfileInformationForm />
        <UpdatePasswordForm class="q-py-sm"/>
      </div>
    </div>
  </authenticated-layout>
</template>

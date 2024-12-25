<script setup>
const title = "Dashboard";
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="q-ma-md">
      <div class="row q-my-sm">
        <q-card class="col" square flat bordered>
          <q-card-section>
            <div class="text-h6">Dashboard</div>
            <p class="q-my-md">Dashboard Works!!</p>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>

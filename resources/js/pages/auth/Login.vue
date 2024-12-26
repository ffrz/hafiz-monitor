<script setup>
import { handleSubmit } from "@/helpers/client-req-handler";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

let form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => handleSubmit({ form, url: route("login") });
</script>

<template>
  <i-head title="Login" />
  <guest-layout>
    <q-page class="row justify-center items-center">
      <div class="column">
        <div class="row">
          <q-form class="q-gutter-md" @submit.prevent="submit">
            <q-card square bordered class="q-pa-md shadow-1">
              <q-card-section>
                <h5 class="q-my-sm text-center">Masuk</h5>
              </q-card-section>
              <q-card-section>
                <q-input
                  v-model.trim="form.email"
                  label="Email"
                  lazy-rules
                  type="email"
                  :error="!!form.errors.email"
                  autocomplete="email"
                  :error-message="form.errors.email"
                  :disable="form.processing"
                  :rules="[
                    (val) => (val && val.length > 0) || 'Masukkan email',
                  ]"
                >
                  <template v-slot:append>
                    <q-icon name="person" />
                  </template>
                </q-input>
                <q-input
                  v-model="form.password"
                  type="password"
                  label="Kata Sandi"
                  :error="!!form.errors.password"
                  autocomplete="current-password"
                  :error-message="form.errors.password"
                  lazy-rules
                  :disable="form.processing"
                  :rules="[
                    (val) => (val && val.length > 0) || 'Masukkan kata sandi',
                  ]"
                >
                  <template v-slot:append>
                    <q-icon name="key" />
                  </template>
                </q-input>
                <q-checkbox
                  class="q-mt-sm q-pl-none"
                  style="margin-left: -10px"
                  v-model="form.remember"
                  :disable="form.processing"
                  label="Ingat saya di perangkat ini"
                />
              </q-card-section>
              <q-card-actions>
                <q-btn
                  icon="login"
                  type="submit"
                  color="primary"
                  class="full-width"
                  label="Login"
                  :disable="form.processing"
                />
              </q-card-actions>
              <q-card-section class="text-center q-pa-none q-mt-md">
                <p class="q-my-xs text-grey-7">
                  Belum punya akun?
                  <i-link :href="route('register')">Daftar</i-link>
                </p>
                <p class="q-my-xs text-grey-7">
                  Lupa sandi?
                  <i-link :href="route('forgot-password')"
                    >Atur ulang kata sandi</i-link
                  >
                </p>
              </q-card-section>
            </q-card>
          </q-form>
        </div>
      </div>
    </q-page>
  </guest-layout>
</template>

<style>
.q-card {
  width: 360px;
}
</style>

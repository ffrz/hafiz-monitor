<script setup>
import { handleSubmit } from "@/helpers/client-req-handler";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

let form = useForm({
  email: "",
  password: "",
  remember: false,
});
const showPassword = ref(false);
const submit = () => handleSubmit({ form, url: route("login") });
</script>

<template>
  <i-head title="Login" />
  <guest-layout>
    <q-page class="row justify-center items-center">
      <div class="column">
        <div class="row">
          <q-form class="q-gutter-md" @submit.prevent="submit">
            <q-card square bordered class="q-pt-sm q-pb-md q-px-md shadow-1">
              <q-card-section>
                <h6
                  class="q-my-none text-center text-subtitle1 text-bold text-grey-8"
                >
                  Selamat Datang Kembali
                </h6>
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
                  :type="showPassword ? 'text' : 'password'"
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
                    <q-btn
                      dense
                      flat
                      round
                      @click="showPassword = !showPassword"
                      ><q-icon :name="showPassword ? 'key_off' : 'key'"
                    /></q-btn>
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
                <div class="full-width">
                  <q-btn
                    icon="login"
                    type="submit"
                    color="primary"
                    class="full-width"
                    label="Login"
                    :disable="form.processing"
                  />
                </div>
              </q-card-actions>
              <q-card-section class="text-center q-pa-none q-mt-md">
                <p class="q-my-xs text-grey-7">
                  Belum punya akun?
                  <i-link :href="route('register-options')">Daftar</i-link>
                </p>
                <p class="q-my-xs text-grey-7">
                  Lupa kata sandi?
                  <i-link :href="route('password.request')">Atur ulang</i-link>
                </p>
              </q-card-section>
              <q-card-section
                class="flex justify-center items-center q-px-sm q-py-xs"
              >
                <hr class="col line" />
                <span class="col-auto q-mx-sm">Atau</span>
                <hr class="col line" />
              </q-card-section>
              <q-card-actions>
                <div class="full-width">
                  <q-btn
                    icon="login"
                    href="/auth/google/redirect"
                    color="accent"
                    class="full-width"
                    label="Gunakan akun Google"
                    :disable="form.processing"
                  />
                </div>
              </q-card-actions>
            </q-card>
          </q-form>
        </div>
      </div>
    </q-page>
  </guest-layout>
</template>

<style scoped>
.q-card {
  width: 360px;
}

.line {
  border: none;
  border-top: 1px solid #ddd;
}
</style>

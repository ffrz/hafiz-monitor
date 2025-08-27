<script setup>
import { handleSubmit } from "@/helpers/client-req-handler";
import { scrollToFirstErrorField } from "@/helpers/utils";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const nameInputRef = ref();
const page = usePage();
const user = page.props.auth.user;
const form = useForm({
  name: user.name,
  email: user.email,
});

const submit = () => handleSubmit({ form, url: route("profile.update") });
</script>

<template>
  <q-form
    class="row"
    @submit.prevent="submit"
    @validation-error="scrollToFirstErrorField"
  >
    <q-card square flat bordered class="col">
      <q-card-section>
        <div class="text-h6 q-my-xs text-subtitle1">Profil Saya</div>
        <p class="text-caption text-grey-9">Perbarui profil saya.</p>
        <q-input
          ref="nameInputRef"
          v-model.trim="form.name"
          label="Nama"
          :disable="form.processing"
          lazy-rules
          :error="!!form.errors.name"
          :error-message="form.errors.name"
          :rules="[(val) => (val && val.length > 0) || 'Name harus diisi.']"
          hide-bottom-space
        />
        <q-input
          v-model.trim="form.email"
          label="Email"
          readonly=""
          :disable="form.processing"
          hide-bottom-space
        />
      </q-card-section>
      <q-card-section>
        <q-btn
          type="submit"
          color="primary"
          label="Perbarui Profil Saya"
          :disable="form.processing"
          icon="save"
        />
      </q-card-section>
    </q-card>
  </q-form>
</template>

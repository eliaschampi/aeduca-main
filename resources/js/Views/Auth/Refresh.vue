<script setup>
import { Form } from "vee-validate";
import { computed, inject, ref } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import { useRefreshStore } from "../../pinia/refresh";
import { useUserStore } from "../../pinia/user";
const emitter = inject("emitter");
const user = computed(() => useUserStore().user.name);
const store = useRefreshStore();
const showPass = ref(false);
function handleOnCheck() {
  store.onCheck(() => emitter.emit("reauthed"));
}
</script>

<template>
  <Dialog id="reauth" :title="store.title" @ok="handleOnCheck">
    <Alert>
      <div class="text-center">
        <b>{{ user }} </b>, debemos asegurarnos de que eres tú.
      </div>
    </Alert>
    <Form v-slot="{ meta }" :validation-schema="{ password: 'required|min:5|max:12' }" @submit="handleOnCheck">
      <div class="space-b-1 text-center">
        <MInput
          v-model="store.password"
          name="password"
          :type="showPass ? 'text' : 'password'"
          class="mb-4"
          label="Ingresa tu contraseña"
        />
        <div class="text-start">
          <MCheck v-model="showPass" :text="!showPass ? 'Mostrar contraseña' : 'Ocultar contraseña'" />
        </div>
        <Alert v-show="store.error" type="alert-danger">
          {{ store.error }}
        </Alert>
        <MSubmit :disabled="!meta.valid" size="btn-lg" color="btn-inverse-success"> Refrescar </MSubmit>
      </div>
    </Form>
    <template #foot>
      <div class="text-center bg-body p-3 rounded">
        <div class="pointer fst-italic" @click="store.cleanAndExit()">
          <i class="text-danger icon ion-ios-log-out"></i> Cerrar sesión
        </div>
      </div>
    </template>
  </Dialog>
</template>

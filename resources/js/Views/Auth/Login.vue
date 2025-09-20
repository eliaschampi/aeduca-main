<script setup>
import { reactive, ref } from "vue";
import { loginApi } from "../../http/auth";
import LoginForm from "./LoginForm.vue";
const isLoading = ref(false);
const user = reactive({
  email: "",
  password: "",
});
const res = reactive({
  message: "",
  alert: "",
});
const showPass = ref(false);
async function handleSubmitClick() {
  isLoading.value = true;
  Object.assign(res, { message: "", alert: "" });
  try {
    await loginApi(user);
    res.message = "Sesión iniciada correctamente";
    res.alert = "alert-success";
    setTimeout(() => {
      isLoading.value = false;
      location.reload();
    }, 3000);
  } catch (error) {
    res.message = error.message;
    res.alert = "alert-danger";
    isLoading.value = false;
  }
}
const schema = {
  email: {
    required: true,
    email: true,
    max: 100,
  },
  password: {
    required: true,
    min: 6,
    max: 50,
  },
};
</script>

<template>
  <LoginForm
    title="Iniciar Sesión"
    subtitle="Ingrese sus credenciales de autenticación para acceder."
    :res="res"
    :is-loading="isLoading"
    btn="ACCEDER"
    :schema="schema"
    @onsub="handleSubmitClick"
  >
    <div class="space-b-1 mb-3">
      <MInput v-model="user.email" name="email" label="Correo" placeholder="Ejm. user@aedu..." />
      <MInput
        v-model="user.password"
        name="password"
        label="Contraseña"
        :type="showPass ? 'text' : 'password'"
        placeholder="*******"
      />
      <MCheck v-model="showPass" :text="showPass ? 'Ocultar contraseña' : 'Mostrar contraseña'" />
    </div>
    <template #foot>
      <RouterLink :to="{ name: 'recover' }" class="btn btn-success btn-sm text-dark">
        <i class="icon ion-md-lock"></i> Olvidé mi contraseña
      </RouterLink>
    </template>
  </LoginForm>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { recoverApi } from "../../http/auth";
import LoginForm from "./LoginForm.vue";
const isLoading = ref(false);
const router = useRouter();
const res = reactive({
    message: "",
    alert: "",
});
const recoverData = reactive({
    email: "",
    dni: "",
});

async function handleSubmitClick() {
    isLoading.value = true;
    Object.assign(res, { message: "", alert: "" });
    try {
        const { message } = await recoverApi(recoverData);
        res.message = message;
        res.alert = "alert-success";
        setTimeout(() => {
            router.push({ name: "login" });
        }, 3000);
    } catch {
        res.message = "Solicitud rechazada. Asegurate de ingresar sus datos correctamente";
        res.alert = "alert-danger";
    } finally {
        isLoading.value = false;
    }
}

const schema = {
    email: {
        required: true,
        email: true,
    },
    dni: {
        required: true,
        numeric: true,
        len: 8,
    },
};
</script>
<template>
    <LoginForm
        title="Recuperar contraseña"
        subtitle="Ingresa los campos requeridos y solicita tu nueva clave"
        :res="res"
        :is-loading="isLoading"
        btn="ENVIAR"
        :schema="schema"
        @onsub="handleSubmitClick"
    >
        <m-input
            v-model="recoverData.dni"
            class="mb-4"
            name="dni"
            type="text"
            label="Ingresa tu DNI"
        />
        <m-input
            v-model="recoverData.email"
            class="mb-4"
            name="email"
            type="email"
            label="Ingresa tu Correo"
        />
        <template #foot>
            <RouterLink class="btn btn-success btn-sm text-dark" :to="{ name: 'login' }">
                <i class="icon ion-md-log-in"></i> Volver a login
            </RouterLink>
        </template>
    </LoginForm>
</template>

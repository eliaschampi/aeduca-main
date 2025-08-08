<script setup>
import { computed, ref } from "vue";
import { sendToSupport } from "../../http";
import { useAppStore } from "../../pinia/app";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
const main = useAppStore();
const message = ref("");
const { successSnack } = useSnackbarStore();
async function sendMessage() {
    const { dni, fullname } = useUserStore();
    const { data } = await sendToSupport({
        dni,
        content: `user ${fullname} message: ${message.value}`,
    });
    successSnack(data.message);
    message.value = "";
}

const disableFeedbackBtn = computed(() => {
    return message.value.length < 10 || message.value.length > 380;
});
</script>

<template>
    <section style="max-width: 40rem">
        <card :title="main.myapp">
            <p>
                {{ main.myapp }} es una plataforma educativa integral en la nube que ofrece funcionalidades de gestión
                de procesos académicos y administrativos de forma centralizada y eficiente, tambien provee funciones
                interactivas y contenido educativo bajo demanda para su comunidad de usuarios.
            </p>
            <p>
                <span class="fw-bold"> Año: </span>
                <b>
                    {{ new Date().getFullYear() }}
                </b>
            </p>
            <p>
                <span class="fw-bold"> Versión de la aplicación: </span>
                <b class="badge bg-success p-2">
                    {{ main.version }}
                </b>
            </p>
            <p>
                <span class="fw-bold">Fecha de actualización:</span>
                24 de Agosto del 2024 a las 21:00.
            </p>
            <p>
                <span class="fw-bold">Desarrollador:</span>
                <a
                    class="blue-text"
                    href="https://pe.linkedin.com/in/eliaschampi"
                    target="_blank"
                > Elias Champi. </a>
            </p>

            <template #foot>
                <code class="mb-3 opacity-50">
                    <strong>{{ new Date().getFullYear() }}. Todos los derechos reservados &#169;EliasChampi</strong>
                    <small class="d-block fst-italic">Este proyecto está protegido por la licencia GNU/GPL3</small>
                </code>
            </template>
        </card>
        <panel
            class="mt-4 space-b-1"
            title="Envía un mensaje al desarrollador"
        >
            <div class="opacity-50">Puedes enviar un reporte de error, duda o un saludo.</div>
            <form
                class="space-b-1"
                @submit.prevent="sendMessage"
            >
                <m-textarea
                    v-model="message"
                    name="message"
                >
                    Escribe aquí tu mensaje
                    <small>{{ 380 - message.length }} restantes</small>
                </m-textarea>
                <m-submit
                    :disabled="disableFeedbackBtn"
                    color="btn-success"
                    icon="icon ion-md-send"
                >
                    Enviar
                </m-submit>
            </form>
        </panel>
    </section>
</template>

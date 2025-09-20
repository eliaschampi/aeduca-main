<script setup>
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";
const route = useRoute();
const router = useRouter();

const historyRoutes = computed(() => {
    const dni = route.params.dni;
    return [
        {
            name: "es_register",
            label: "Historial de matrículas",
            params: { dni },
        },
        {
            name: "es_family",
            label: "Apoderados",
            params: { dni },
        },
        {
            name: "es_doc",
            label: "Información adjunta",
            params: { dni },
        },
        {
            name: "attendance_student",
            label: "Asistencia",
            params: { dni },
        },
        {
            name: "incidence_student",
            label: "Atenciones e Incidencias",
            params: { dni },
        },
    ];
});

const historyRoute = computed({
    get() {
        return historyRoutes.value.find((item) => item.name === route.name);
    },
    set(value) {
        router.push(value);
    },
});
</script>

<template>
    <div class="space-b">
        <m-select
            v-model="historyRoute"
            name="history"
            label="Información del estudiante"
            standalone
            :options="historyRoutes"
            opt-label="label"
            opt-value="all"
        />
        <RouterView />
    </div>
</template>

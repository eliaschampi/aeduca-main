<script setup>
import { computed } from "vue";
import { useUserStore } from "../../pinia/user";
import Info from "../Person/Info.vue";
import { useRoute } from "vue-router";
const dni = computed(() => useRoute().params.dni);
const branch = computed(() => useUserStore().branch);
</script>
<template>
    <Info
        id="student-info"
        who="student"
    >
        <div class="d-flex align-items-center">
            <div class="fw-medium">Sede al que pertenece:</div>
            <div class="ms-2 badge bg-light text-dark fw-bold">
                <b>{{ branch }}</b>
            </div>
        </div>
        <hr>
        <m-router
            class="me-2"
            color="btn-primary"
            :to="{ name: 'payment', params: { dni } }"
        >
            Pagos
        </m-router>
    </Info>
</template>

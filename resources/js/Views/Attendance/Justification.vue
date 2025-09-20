<script setup>
import { ref, watchEffect } from "vue";
import { dformat } from "../../core/day";
import { fetchByEntityJsApi } from "../../http";
import { useStudentStore } from "../../pinia/student";
import Justification from "./components/Justification.vue";
const jusRef = ref(null);
const justifications = ref([]);
const isLoading = ref(false);
const columns = ["Nro", "Enviado el:", "Para fecha:", "Justificación", "Estado de asis:", "#"];
const formatedDate = (date) => dformat(date, "dddd DD");

async function fetchData(dni) {
    isLoading.value = true;
    const { data } = await fetchByEntityJsApi(dni);
    justifications.value = data.values;
    isLoading.value = false;
}

watchEffect(() => {
    const { dni } = useStudentStore();
    fetchData(dni);
});
</script>
<template>
    <section id="JustificationID">
        <alert class="mb-4">
            Los permisos y las justificaciónes son enviados desde la plataforma por el usuario,
            aunque también pueden ser generados por los administradores. Revisa y gestiona las
            justificaciones aqui
        </alert>
        <m-table :columns="columns" :is-loading="isLoading" :head="false" :data="justifications">
            <template #data="{ rows }">
                <tr v-for="item in rows" :key="item.code">
                    <td>{{ item.attendance_code }}</td>
                    <td>{{ formatedDate(item.created_at) }}</td>
                    <td>{{ formatedDate(item.attendance.created_at) }}</td>
                    <td>
                        <span class="text-dotted text-secondary">
                            {{ item.description }}
                        </span>
                    </td>
                    <td>
                        <b>
                            {{ item.attendance.state }}
                        </b>
                    </td>
                    <td>
                        <m-action
                            color="info"
                            icon="eye"
                            tool="Ver Detalles"
                            @action="
                                jusRef.handleShowModal(
                                    item.attendance_code,
                                    item.attendance.state,
                                    item,
                                )
                            "
                        />
                    </td>
                </tr>
            </template>
        </m-table>

        <Justification ref="jusRef" @done="fetchData" />
    </section>
</template>

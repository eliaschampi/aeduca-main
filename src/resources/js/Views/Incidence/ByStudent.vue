<script setup>
import { ref, watchEffect } from "vue";
import { useRouter } from "vue-router";
import { downl } from "../../core/util";
import types from "../../data/inTypes.json";
import { fetchByEntityInApi, printInApi } from "../../http";
import { useIncidenceStore } from "../../pinia/incidence";
import { useStudentStore } from "../../pinia/student";
import { useUserStore } from "../../pinia/user";
const isLoading = ref(false);
const incidences = ref([]);
const columns = ["Nro", "Usuario", "Motivo", "Tipo", "Fecha de registro", "#"];
const { isMe } = useUserStore();
const router = useRouter();
async function fetchData(dni) {
    isLoading.value = true;
    const { data } = await fetchByEntityInApi(dni);
    incidences.value = data.values;
    isLoading.value = false;
}

async function handlePrint(code) {
    const { data } = await printInApi(code);
    downl(data, `Atencion Nro ${code}`);
}

function handleEditClick(item) {
    const { setIncidence } = useIncidenceStore();
    setIncidence(item);
    router.push({ name: "new_incidence", params: { code: item.code } });
}

watchEffect(() => {
    const { dni } = useStudentStore();
    fetchData(dni);
});
</script>
<template>
    <m-table :columns="columns" :is-loading="isLoading" :head="false" :data="incidences">
        <template #data="{ rows }">
            <tr v-for="item in rows" :key="item.code">
                <td>{{ item.code }}</td>
                <td>{{ item.user.name }}</td>
                <td>
                    {{ item.title }}
                </td>
                <td>
                    <span class="badge bg-secondary">
                        {{ types[item.type] }}
                    </span>
                </td>
                <td>
                    {{ $datetim(item.created_at) }}
                </td>
                <td>
                    <m-action v-show="isMe(item.user_code)" @action="handleEditClick(item)" />
                    <m-action
                        icon="print"
                        color="info"
                        tool="Exportar"
                        @action="handlePrint(item.code)"
                    />
                </td>
            </tr>
        </template>
    </m-table>
</template>

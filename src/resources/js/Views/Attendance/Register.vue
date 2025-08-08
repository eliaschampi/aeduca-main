<script setup>
import { computed, ref } from "vue";
import MySection from "../../Components/Views/MySection.vue";
import Priority from "../../Components/Views/Priority.vue";
import { fetchForAttendanceApi, storeAttApi } from "../../http";
import { useDegreeStore } from "../../pinia/degree";
import { usePriorityStore } from "../../pinia/priority";
import { useSectionStore } from "../../pinia/section";
const columns = ["Estudiante", "Celular", "Hora de ingreso", "Estado", "Registrar"];
const { code: degree_code } = useDegreeStore();
const sterm = ref("");
const students = ref([]);

const filtered = computed(() => {
    const re = new RegExp(sterm.value, "i");
    const result = students.value.filter((item) => re.test(item.full_name));
    return result.sort((a, b) => (a.full_name > b.full_name ? 1 : -1));
});

async function fetchData() {
    const { priority } = usePriorityStore();
    const { code } = useSectionStore();
    const { data } = await fetchForAttendanceApi(code, priority);
    students.value = data.values;
}

async function handleSubmitClick(item) {
    const { priority } = usePriorityStore();
    await storeAttApi({ ...item, entity_type: "s", priority });
    students.value.splice(students.value.indexOf(item), 1);
}
function disableSaveButton(item) {
    return ["presente", "tarde"].includes(item.state) ? item.entry_time === "" : false;
}
</script>
<template>
    <card title="Registrar asistencia del día de hoy">
        <template #rb>
            <Priority @updated="fetchData" />
        </template>
        <alert class="alert alert-success" :dismisable="false">
            Listado de Estudiantes que aún no han registrado su asistencia. Para ver a los
            estudiantes que registrarón sus asistencia
            <RouterLink
                :to="{
                    name: 'main_attendance',
                    params: { degree_code },
                }"
                class="fw-medium text-title"
            >
                Click aquí
            </RouterLink>
        </alert>

        <m-table v-model="sterm" :columns="columns" :data="filtered" @refresh="fetchData">
            <MySection @done="fetchData" />
            <template #data="{ rows }">
                <tr v-for="item in rows" :key="item.entity_identifier">
                    <td class="text-primary-emphasis fw-medium">
                        {{ item.full_name }}
                    </td>
                    <td>
                        <i class="icon ion-md-call icon-sm text-success"></i>
                        {{ item.phone }}
                    </td>
                    <td>
                        <select
                            v-model="item.state"
                            style="min-width: 5rem"
                            class="form-select form-select-sm"
                        >
                            <option value="permiso">Permiso</option>
                            <option value="falta">falta</option>
                            <option value="presente">Presente</option>
                            <option value="tarde">Tarde</option>
                        </select>
                    </td>
                    <td>
                        <input
                            v-show="['presente', 'tarde'].includes(item.state)"
                            v-model="item.entry_time"
                            style="max-width: 7rem"
                            class="form-control form-control-sm"
                            step="1"
                            type="time"
                        />
                    </td>
                    <td>
                        <m-button
                            :disabled="disableSaveButton(item)"
                            color="btn-success btn-icon"
                            icon="ion-md-checkmark"
                            @ok="handleSubmitClick(item)"
                        />
                    </td>
                </tr>
            </template>
        </m-table>
        <template #foot>
            <footext :title="`${students.length} asistencias registradas`" />
        </template>
    </card>
</template>

<style scoped>
input[type="time"] {
    font-weight: bold;
    width: 9em;
}
</style>

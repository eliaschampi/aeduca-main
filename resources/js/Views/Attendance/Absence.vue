<script setup>
import { computed, ref, watch } from "vue";
import Cyclepick from "../../Components/Views/Cyclepick.vue";
import Datepick from "../../Components/Views/Datepick.vue";
import PersonLink from "../../Components/Views/PersonLink.vue";
import Priority from "../../Components/Views/Priority.vue";
import { useAttendanceToolkit } from "../../composables/attendance";
import { absencesAttApi } from "../../http";
import { useCycleStore } from "../../pinia/cycle";
import { useDateStore } from "../../pinia/date";
import { usePriorityStore } from "../../pinia/priority";
import AttendanceRow from "./components/AttendanceRow.vue";
import Justification from "./components/Justification.vue";
const columns = [
    "#",
    "Estudiante",
    "Grupo/Secc.",
    "Celular",
    "Ingreso Registrado",
    "Estado",
    "Aciones",
];
const attendances = ref([]);
const sterm = ref("");
const isLoading = ref(false);
const jusRef = ref(null);
const { cancelEdit, handleEdit, mod, states, handleUpdate } = useAttendanceToolkit();

const current = computed(() => useCycleStore().current);

const filtered = computed(() => {
    return attendances.value.filter((item) => {
        return new RegExp(sterm.value, "i").test([item.person.name, item.person.lastname].join());
    });
});

async function fetchData() {
    isLoading.value = true;
    const { date } = useDateStore();
    const { priority } = usePriorityStore();
    const { data } = await absencesAttApi(current.value, date, priority);
    attendances.value = data.values;
    isLoading.value = false;
}

watch(current, fetchData);
</script>
<template>
    <section id="absence-report">
        <panel title="Ciclos activos">
            <Cyclepick />
        </panel>
        <card title="Tardanzas e Inasistencias" class="mt-4">
            <template #rb>
                <Priority @updated="fetchData" />
            </template>
            <m-table
                v-model="sterm"
                :columns="columns"
                :data="filtered"
                :is-loading="isLoading"
                @refresh="fetchData"
            >
                <div class="d-flex space mt-3 flex-wrap">
                    <Datepick style="max-width: 13rem" @fetch="fetchData" />
                </div>
                <template #data="{ rows }">
                    <AttendanceRow
                        v-for="item in rows"
                        :key="item.code"
                        v-model:state="item.state"
                        v-model:time="item.entry_time"
                        :mod="mod"
                        :code="item.code"
                        :has-jus="!item.justification"
                        :state-label="states[item.state]"
                        @update="handleUpdate(item)"
                        @cancel="cancelEdit(item)"
                        @edit="handleEdit(item)"
                        @show-jus-modal="
                            jusRef.handleShowModal(item.code, item.state, item.justification)
                        "
                    >
                        <td><i class="icon ion-md-folder icon-md text-secondary"></i></td>
                        <td>
                            <PersonLink :person="item.person" route="student_profile" />
                        </td>
                        <td>
                            {{ item.section_code.substring(8) }}
                        </td>
                        <td>
                            <i class="icon ion-md-call icon-sm text-success"></i>
                            {{ item.person.phone }}
                        </td>
                    </AttendanceRow>
                </template>
            </m-table>
            <template #foot>
                <footext :title="`${attendances.length} estudiantes faltaron o llegaron tarde`" />
            </template>
            <Justification ref="jusRef" @done="fetchData" />
        </card>
    </section>
</template>

<script setup>
import { computed, ref } from "vue";
import Datepick from "../../Components/Views/Datepick.vue";
import PersonLink from "../../Components/Views/PersonLink.vue";
import { useAttendanceToolkit, useConfigData } from "../../composables/attendance";
import { fetchForTeacherAttApi } from "../../http";
import { useDateStore } from "../../pinia/date";
import AttendanceRow from "./components/AttendanceRow.vue";
import Justification from "./components/Justification.vue";

const columns = ["#", "Docente", "Celular", "Hora de ingreso", "Estado", "Acciones"];
const attendances = ref([]);
const sterm = ref("");
const jusRef = ref(null);
const { configData } = useConfigData();
const { states, handleUpdate, cancelEdit, handleEdit, mod } = useAttendanceToolkit();

const filtered = computed(() =>
    attendances.value.filter((item) =>
        new RegExp(sterm.value, "i").test([item.person.name, item.person.lastname].join()),
    ),
);

async function fetchData() {
    const { date } = useDateStore();
    const { data } = await fetchForTeacherAttApi(date);
    attendances.value = data.values;
}
</script>
<template>
    <section id="teacherAtt">
        <card title="Asistencia diaria de docentes">
            <alert>
                El horario de ingreso para los docentes es {{ configData.entry_time }} con una
                tolerancia de {{ configData.tolerance }} minutos.
            </alert>
            <m-table v-model="sterm" :columns="columns" :data="filtered" :fetch="fetchData">
                <Datepick @fetch="fetchData" />
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
                            <PersonLink :person="item.person" route="teacher_profile" />
                        </td>
                        <td>
                            <i class="icon ion-md-call icon-sm text-success"></i>
                            {{ item.person.phone }}
                        </td>
                    </AttendanceRow>
                </template>
            </m-table>
            <template #foot>
                <footext :title="`${attendances.length} asistencias registradas`" />
            </template>
        </card>
        <Justification ref="jusRef" @done="fetchData" />
    </section>
</template>

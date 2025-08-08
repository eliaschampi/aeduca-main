<script setup>
import { computed, ref } from "vue";
import Range from "../../Components/Ui/Range.vue";
import { useAttendanceToolkit, useConfigData } from "../../composables/attendance";
import { diffToDate, dparseFromFormat } from "../../core/day.js";
import { downl } from "../../core/util";
import { exportToExcelAttApi, fetchByEntityAttApi } from "../../http";
import { useDateStore } from "../../pinia/date";
import { useTeacherStore } from "../../pinia/teacher";
import AttendanceRow from "./components/AttendanceRow.vue";
import Justification from "./components/Justification.vue";
const columns = ["Fecha y hora", "Tardanza", "Ingreso registrado", "Estado de Asis.", "Acciones"];
const total_delay = ref(0);
const jusRef = ref(null);
const attendances = ref([]);
const storedDni = computed(() => useTeacherStore().dni);
const dstore = useDateStore();
const { configData } = useConfigData();
const { states, handleUpdate, handleEdit, cancelEdit, mod } = useAttendanceToolkit();

async function fetchData() {
    total_delay.value = 0;
    const { data } = await fetchByEntityAttApi({
        ...dstore.range,
        entity_identifier: storedDni.value,
        priority: 1,
    });
    const values = data.values;
    const atts = [];
    values.forEach((item) => {
        let delay = 0;
        if (item.entry_time) {
            const from = dparseFromFormat(configData.entry_time, "HH:mm");
            const d = dparseFromFormat(item.entry_time, "HH:mm:ss");
            delay = diffToDate(from, d, "m") - Number(configData.tolerance);
        }
        if (delay > 0) {
            item.delay = delay;
            total_delay.value += delay;
        }
        atts.push(item);
    });
    attendances.value = atts;
}

async function excel() {
    const { fullname } = useTeacherStore();
    const { data } = await exportToExcelAttApi(storedDni.value, dstore.range.from, dstore.range.to);
    const name = `Asistencia ${fullname} ${dstore.range.from} Hasta ${dstore.range.to}`;
    downl(data, name, ".xlsx");
}
</script>
<template>
    <section>
        <alert>
            El horario de ingreso para los docentes es {{ configData.entry_time }} con una
            tolerancia de {{ configData.tolerance }} minutos.
        </alert>
        <m-table :columns="columns" :data="attendances" :fetch="fetchData">
            <Range @fetch="fetchData" />
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
                    <td>{{ $datetim(item.created_at) }}</td>
                    <td>
                        <span v-show="item.delay"> {{ item.delay }} minutos</span>
                    </td>
                </AttendanceRow>
                <tr>
                    <td colspan="2" class="text-secondary">Total de minutos de tardanza</td>
                    <td colspan="4">{{ total_delay }} minutos</td>
                </tr>
            </template>
        </m-table>
        <div class="d-flex justify-content-between">
            <span class="fw-bold opacity-50">
                Cantidad de Asistencias: {{ attendances.length }}
            </span>
            <m-button
                :disabled="!attendances.length"
                color="btn-warning btn-icon"
                icon="icon ion-md-cloud-download"
                @ok="excel"
            />
        </div>
        <Justification ref="jusRef" @done="fetchData" />
    </section>
</template>

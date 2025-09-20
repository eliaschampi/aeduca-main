<script setup>
import { computed, ref, watch } from "vue";
import Range from "../../Components/Ui/Range.vue";
import Priority from "../../Components/Views/Priority.vue";
import { useAttendanceToolkit } from "../../composables/attendance";
import { downl } from "../../core/util";
import { exportCVAttApi, fetchByEntityAttApi } from "../../http";
import { useDateStore } from "../../pinia/date";
import { usePriorityStore } from "../../pinia/priority";
import { useStudentStore } from "../../pinia/student";
import AttendanceRow from "./components/AttendanceRow.vue";
import Justification from "./components/Justification.vue";
const attendances = ref([]);
const columns = ["Fecha y hora", "Ingreso registrado", "Estado de Asis.", "Acciones"];
const storedDni = computed(() => useStudentStore().dni);
const jusRef = ref(null);
const { states, handleUpdate, cancelEdit, handleEdit, mod } = useAttendanceToolkit();

function getEssentialProperties() {
  const { priority } = usePriorityStore();
  const { range } = useDateStore();
  return { priority, range };
}

async function fetchData() {
  const { priority, range } = getEssentialProperties();
  const { data } = await fetchByEntityAttApi({
    ...range,
    entity_identifier: storedDni.value,
    priority,
  });
  attendances.value = data.values;
}

async function exportCV() {
  const { priority, range } = getEssentialProperties();
  const { fullname } = useStudentStore();
  const { data } = await exportCVAttApi(storedDni.value, range.from, range.to, priority);
  const name = `Asistencia ${fullname} ${range.from} Hasta ${range.to}`;
  downl(data, name, ".pdf");
}

watch(storedDni, fetchData);
</script>

<template>
  <section>
    <MTable :columns="columns" :data="attendances" :fetch="fetchData">
      <Range @fetch="fetchData">
        <Priority :is-sm="false" @updated="fetchData" />
      </Range>
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
          @show-jus-modal="jusRef.handleShowModal(item.code, item.state, item.justification)"
        >
          <td>{{ $date(item.created_at) }}</td>
        </AttendanceRow>
      </template>
      <template #foot>
        <Footext>
          <MButton color="btn-inverse-primary" size="btn-sm" @ok="exportCV"> Constancia </MButton>
        </Footext>
      </template>
    </MTable>
    <Footext :title="`${attendances.length} asistencias registradas`" />
    <Justification ref="jusRef" @done="fetchData" />
  </section>
</template>

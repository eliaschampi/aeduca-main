<script setup>
import { computed, ref } from "vue";
import Datepick from "../../Components/Views/Datepick.vue";
import MySection from "../../Components/Views/MySection.vue";
import PersonLink from "../../Components/Views/PersonLink.vue";
import Priority from "../../Components/Views/Priority.vue";
import { useAttendanceToolkit } from "../../composables/attendance";
import { downl } from "../../core/util";
import { exportToExcelAttApi, fetchBySectionAttApi } from "../../http";
import { useDateStore } from "../../pinia/date";
import { usePriorityStore } from "../../pinia/priority";
import { useSectionStore } from "../../pinia/section";
import AttendanceRow from "./components/AttendanceRow.vue";
import Justification from "./components/Justification.vue";
const columns = ["#", "Estudiante", "Celular", "Hora de ingreso", "Estado", "Acciones"];
const isLoading = ref(false);
const attendances = ref([]);
const sterm = ref("");
const jusRef = ref(null);
const { states, handleUpdate, cancelEdit, handleEdit, mod } = useAttendanceToolkit();

const filtered = computed(() =>
  attendances.value.filter((item) => new RegExp(sterm.value, "i").test([item.person.name, item.person.lastname].join()))
);

function getEssentialProperties() {
  const { code } = useSectionStore();
  const { priority } = usePriorityStore();
  const { date } = useDateStore();
  return { code, priority, date };
}

async function fetchData() {
  isLoading.value = true;
  const { code, priority, date } = getEssentialProperties();
  const { data } = await fetchBySectionAttApi({
    section_code: code,
    date,
    priority,
  });
  attendances.value = data.values;
  isLoading.value = false;
}

async function excel() {
  const { code, priority, date } = getEssentialProperties();
  const { data } = await exportToExcelAttApi(code, date, priority);
  const name = `Asistencia ${code} fecha ${date}`;
  downl(data, name, ".xlsx");
}
</script>

<template>
  <Card title="Asistencia por sección y fecha">
    <template #rb>
      <div class="d-flex">
        <Priority @updated="fetchData" />
        <MButton
          class="btn-icon"
          :disabled="!attendances.length"
          color="btn-warning"
          icon="icon ion-md-cloud-download"
          @ok="excel"
        />
      </div>
    </template>
    <MTable v-model="sterm" :columns="columns" :data="filtered" :is-loading="isLoading" @refresh="fetchData">
      <div class="d-flex space">
        <MySection @done="fetchData" />
        <Datepick @fetch="fetchData" />
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
          @show-jus-modal="jusRef.handleShowModal(item.code, item.state, item.justification)"
        >
          <td><i class="icon ion-md-folder icon-md text-secondary"></i></td>
          <td>
            <PersonLink :person="item.person" route="student_profile" />
          </td>
          <td>
            <i class="icon ion-md-call icon-sm text-success"></i>
            {{ item.person.phone }}
          </td>
        </AttendanceRow>
      </template>
    </MTable>
    <template #foot>
      <Footext :title="`${attendances.length} estudiantes registraron su asistencia`" />
    </template>
    <Justification ref="jusRef" @done="fetchData" />
  </Card>
</template>

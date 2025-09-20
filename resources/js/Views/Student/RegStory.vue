<script setup>
import { computed, ref, watchEffect } from "vue";
import states from "../../data/states.json";
import { fetchRegistersApi, delRegApi } from "../../http";
import { useStudentStore } from "../../pinia/student";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useRegisterStore } from "../../pinia/register";
const storedDni = computed(() => useStudentStore().dni);
const { successSnack, showSnack } = useSnackbarStore();
const prdecorated = {
  0: "Multiple",
  1: "Mañana",
  2: "Tarde",
  3: "Noche",
};
const registers = ref([]);
const fetchRegisters = async (student_dni) => {
  const { data } = await fetchRegistersApi(student_dni);
  registers.value = data.values;
};
function handleDeleteRegister(code) {
  showSnack({
    text: "Se eliminará esta matricula",
    button: "confirmar",
    action: async () => {
      const { data } = await delRegApi(code);
      registers.value.splice(registers.value.indexOf(code), 1);
      const { setRegister } = useRegisterStore();
      setRegister(null);
      successSnack(data.message);
    },
  });
}

watchEffect(() => {
  fetchRegisters(storedDni.value);
});
</script>

<template>
  <MTable :columns="['Codígo', 'Turno', 'Nivel y grado', 'Mes', 'Estado', '#']" :data="registers" :head="false">
    <template #data="{ rows }">
      <tr v-for="item in rows" :key="item.code">
        <td>{{ item.code }}</td>
        <td>{{ prdecorated[item.priority] }}</td>
        <td>{{ item.section_code.substring(8) }} de {{ item.level }}</td>
        <td>{{ $currency(item.monthly) }}</td>
        <td>
          <span class="badge" :class="states[item.state].badge">
            {{ states[item.state].text }}
          </span>
        </td>
        <td>
          <div class="d-flex space">
            <MRouter
              v-show="item.state !== 'f'"
              v-can="'AS'"
              size="btn-sm"
              color="btn-inverse-success"
              class="btn-icon"
              icon="icon ion-md-create"
              :to="{
                name: 'register',
                params: { dni: storedDni },
              }"
            />
            <MButton
              v-show="['i', 'p'].indexOf(item.state) !== -1"
              v-can="'ANS'"
              size="btn-sm"
              color="btn-inverse-danger"
              class="btn-icon"
              icon="icon ion-md-trash"
              @ok="handleDeleteRegister(item.code)"
            />
          </div>
        </td>
      </tr>
    </template>
  </MTable>
</template>

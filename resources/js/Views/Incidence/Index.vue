<script setup>
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import Pagination from "../../Components/Ui/Pagination.vue";
import Month from "../../Components/Views/Month.vue";
import { downl } from "../../core/util";
import types from "../../data/inTypes.json";
import { delInApi, fetchByMonthInApi, printInApi } from "../../http";
import { useDateStore } from "../../pinia/date";
import { useIncidenceStore } from "../../pinia/incidence";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
const dstore = useDateStore();
const { successSnack, showSnack } = useSnackbarStore();
const { fullyear, isMe } = useUserStore();
const columns = ["N°.", "Usuario", "Tipo", "Fecha", "Motivo", "Integrantes", "Acciones"];
const { setIncidence } = useIncidenceStore();
const sterm = ref("");
const incidences = ref([]);
const pagination = ref({});
const router = useRouter();
const filtered = computed(() => {
  const tester = new RegExp(sterm.value, "i");
  return incidences.value.filter((item) => {
    return tester.test(item.title);
  });
});

function processIncidence({ persons, ...rest }) {
  return { persons: persons.map(processPerson), ...rest };
}

function processPerson(person) {
  return {
    dni: person.dni,
    name: person.name,
    lastname: person.lastname,
    entity_type: person.pivot?.entity_type,
    actor_type: person.pivot?.actor_type,
  };
}

async function fetchData(page = 1) {
  const { data } = await fetchByMonthInApi(dstore.month, page);
  const {
    values: { data: results, ...paginationData },
  } = data;
  incidences.value = results.map(processIncidence);
  pagination.value = paginationData;
}

async function handlePrint(code) {
  const { data } = await printInApi(code);
  downl(data, `Atencion Nro ${code}`);
}

function handleEditClick(item) {
  setIncidence(item);
  router.push({ name: "new_incidence", params: { code: item.code } });
}

async function handleDeleteItem(item) {
  showSnack({
    text: "¿Está seguro de eliminar el registro?",
    button: "Confirmar",
    action: async () => {
      const { data } = await delInApi(item.code);
      incidences.value.splice(incidences.value.indexOf(item), 1);
      successSnack(data.message);
    },
  });
}
</script>

<template>
  <Card :title="'Atenciones e incidencias ' + fullyear">
    <template #rb>
      <MRouter
        v-year
        :to="{ name: 'new_incidence' }"
        color="btn-inverse-info"
        class="btn-icon"
        icon="icon ion-md-add"
      />
    </template>
    <MTable v-model="sterm" :columns="columns" :data="filtered" :fetch="fetchData">
      <div class="d-flex align-items-center">
        <Month style="max-width: 9rem" @updated="fetchData()" />
      </div>
      <template #data="{ rows }">
        <tr v-for="item in rows" :key="item.code">
          <td>{{ item.code }}</td>
          <td>{{ item.user.name }}</td>
          <td style="max-width: 10rem">
            {{ types[item.type] }}
          </td>
          <td>{{ $datetim(item.created_at) }}</td>
          <td style="max-width: 10rem">
            <p>{{ item.title }}</p>
          </td>
          <td class="fw-medium">
            <i class="icon ion-md-people icon-sm text-success"></i>
            <template v-if="item.persons.length === 1">
              {{ ` ${item.persons[0].name} ${item.persons[0].lastname}` }}
            </template>
            <template v-else> Varios </template>
          </td>
          <td class="space">
            <MAction icon="print" color="info" tool="Imprimir" @action="handlePrint(item.code)" />
            <template v-if="isMe(item.user_code)">
              <MAction v-show="item.type !== 'pe'" @action="handleEditClick(item)" />
              <MAction icon="trash" color="danger" tool="Eliminar" @action="handleDeleteItem(item)" />
            </template>
          </td>
        </tr>
      </template>
      <template #foot>
        <Pagination :data="pagination" @pagination-change-page="fetchData" />
      </template>
    </MTable>
    <template #foot>
      <Footext :title="`${pagination.total} registros`" />
    </template>
  </Card>
</template>

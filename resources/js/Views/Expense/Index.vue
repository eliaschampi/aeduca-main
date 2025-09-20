<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import Range from "../../Components/Ui/Range.vue";
import Casher from "../../Components/Views/Casher.vue";
import { isToday } from "../../core/date";
import { downl } from "../../core/util";
import { delExApi, exportToExcelExApi, fetchByDatesExApi, printExApi } from "../../http";
import { useCasherStore } from "../../pinia/casher";
import { useDateStore } from "../../pinia/date";
import { useSnackbarStore } from "../../pinia/snackbar";
const router = useRouter();
const { successSnack, showSnack } = useSnackbarStore();
const dstore = useDateStore();
const columns = ["Código", "Fecha", "Caja", "Tipo de Gasto", "Concepto", "Monto", "Acciones"];
const expenses = ref([]);
const sterm = ref("");

const filtered = computed(() => {
  const rex = new RegExp(sterm.value, "i");
  return expenses.value.filter((item) => {
    return rex.test([item.description, item.actiontype.name].join());
  });
});

const total = computed(() => {
  return filtered.value.reduce((acu, item) => {
    return acu + parseFloat(item.total);
  }, 0);
});

async function refreshAll() {
  const { refreshCasher } = useCasherStore();
  await refreshCasher();
  fetchData();
}

async function fetchData() {
  const { user_code } = useCasherStore();
  const { data } = await fetchByDatesExApi(dstore.range, user_code);
  expenses.value = data.values;
}

async function handleExportExcel() {
  const { data } = await exportToExcelExApi(dstore.range);
  const name = `Gastos Desde ${dstore.range.from} Hasta ${dstore.range.to}`;
  downl(data, name, ".xlsx");
}
function handleDeleteExpense(item) {
  showSnack({
    text: "¿Está seguro de eliminar el registro?",
    button: "Confirmar",
    action: async () => {
      const { data } = await delExApi(item.code);
      successSnack(data.message);
      expenses.value = expenses.value.filter((i) => i.code !== item.code);
    },
  });
}
async function handlePrint(code) {
  const { data } = await printExApi(code);
  downl(data, `Reporte de Egreso Nro ${code}`);
}

function handleEdit(item) {
  router.push({ name: "update_expense", params: { code: item.code } });
}

onMounted(async () => {
  const { cashers, refreshCasher, hasUser } = useCasherStore();
  if (!cashers.length) {
    await refreshCasher();
  }
  if (hasUser()) {
    fetchData();
  }
});
</script>

<template>
  <section>
    <Card title="Registro de Gastos">
      <template #rb>
        <div class="d-flex space">
          <MButton
            color="btn-primary"
            class="btn-icon"
            icon="icon ion-md-download"
            :disabled="!expenses.length"
            @ok="handleExportExcel"
          />
          <MRouter
            v-year
            :to="{ name: 'new_expense' }"
            color="btn-inverse-info"
            class="btn-icon"
            icon="icon ion-md-add"
          />
        </div>
      </template>
      <Casher class="my-2" @changed="fetchData" />
      <MTable v-model="sterm" :columns="columns" :data="filtered" @refresh="fetchData">
        <Range @fetch="refreshAll()" />
        <template #data="{ rows }">
          <tr v-for="(item, index) in rows" :key="index">
            <td>{{ item.code }}</td>
            <td>
              <span class="fw-medium">
                {{ $datetim(item.created_at) }}
              </span>
            </td>
            <td>{{ item.cash_code }}</td>
            <td>{{ item.actiontype.name }}</td>
            <td>
              <div style="max-width: 13rem">
                {{ item.description }}
              </div>
            </td>
            <td>{{ $currency(item.total) }}</td>
            <td class="space">
              <MAction icon="eye" tool="Detalle" @action="handleEdit(item)" />
              <MAction icon="print" color="info" tool="Imprimir" @action="handlePrint(item.code)" />
              <MAction
                v-show="isToday(item.created_at)"
                icon="trash"
                color="danger"
                tool="Eliminar"
                @action="handleDeleteExpense(item)"
              />
            </td>
          </tr>
        </template>
        <template #foot>
          <h5 class="text-center my-3">Total de Gastos: {{ $currency(total) }}</h5>
        </template>
      </MTable>
      <template #foot>
        <Footext :title="`${expenses.length} gastos registrados`" />
      </template>
    </Card>
  </section>
</template>

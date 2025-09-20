<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import Range from "../../Components/Ui/Range.vue";
import Casher from "../../Components/Views/Casher.vue";
import { isToday } from "../../core/date";
import { ticket } from "../../core/ticket";
import { downl } from "../../core/util";
import { canceledApi, exportToExcelIncomeApi, fetchByDateApi, fetchDetailApi, printTicketPdfApi } from "../../http";
import { useCasherStore } from "../../pinia/casher";
import { useDateStore } from "../../pinia/date";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
import Cancel from "./components/Cancel.vue";
const { showModal, hideModal } = useModalStore();
const dstore = useDateStore();
const { successSnack } = useSnackbarStore();
const incomes = ref([]);
const details = ref([]);
const selected = reactive({});
const sterm = ref("");
const columns = ["Fecha", "Caja", "Comprobante", "Correlativo", "Tipo", "Cliente", "Total", "Acciones"];
const dColumns = ["Modalidad", "Concepto", "Monto a Pagar", "Descuento", "Importe", "Saldo"];

const types = {
  "00": "Nota de Venta",
  "03": "Boleta de Venta",
};

const filtered = computed(() => {
  const rex = RegExp(sterm.value, "i");
  return incomes.value.filter((item) => {
    return rex.test(item.name.name);
  });
});

const total = computed(() => {
  return filtered.value.reduce((acu, item) => {
    return acu + parseFloat(item.total);
  }, 0);
});

const handleClosed = () => {
  Object.assign(selected, {});
};

const fullSection = (section_code) => {
  return `${section_code.substring(8)} de ${section_code.substring(4, 6)}`;
};

const handleOpen = (item) => {
  Object.assign(selected, item);
  showModal("cancel");
};

async function fetchData() {
  const { user_code } = useCasherStore();
  const { data } = await fetchByDateApi(dstore.range, user_code);
  incomes.value = data.values;
}

async function refreshAll() {
  const { refreshCasher } = useCasherStore();
  await refreshCasher();
  fetchData();
}

async function showDetail(item) {
  const { data } = await fetchDetailApi(item.code);
  Object.assign(selected, item);
  details.value = data.values;
  showModal("incomeDetailModal");
}

async function handleCancel(justification) {
  const payload = {
    code: selected.code,
    justification,
  };
  if (selected.has_register !== null) {
    payload.section_code = selected.has_register.register.section_code;
  } else {
    payload.section_code = "";
  }
  const { data } = await canceledApi(payload);
  incomes.value.splice(incomes.value.indexOf(selected), 1);
  Object.assign(selected, {});
  hideModal("cancel");
  successSnack(data.message);
}

async function handlePrint(item) {
  let result;
  try {
    const { data } = await fetchDetailApi(item.code);
    result = data.values;
    await ticket(item, result);
    successSnack("El ticket se ha enviado a la impresora");
  } catch {
    const { data } = await printTicketPdfApi({
      income: item,
      details: result,
    });
    downl(data, "Reporte de ingreso");
  }
}

async function handleExcel() {
  const { range } = dstore;
  const { data } = await exportToExcelIncomeApi(range);
  const name = `Ingresos Desde ${range.from} Hasta ${range.to}`;
  downl(data, name, ".xlsx");
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
  <section id="mainIncome">
    <Card title="Ingresos">
      <template #rb>
        <div class="d-flex space">
          <MButton
            v-show="incomes.length > 0"
            color="btn-primary"
            class="btn-icon"
            icon="icon ion-md-download"
            @ok="handleExcel"
          />
          <MRouter
            v-can="'AS'"
            v-year
            :to="{ name: 'invoice' }"
            color="btn-inverse-info"
            class="btn-icon"
            icon="icon ion-md-add"
          />
        </div>
      </template>
      <Casher class="my-2" @changed="fetchData" />
      <MTable v-model="sterm" :columns="columns" :data="filtered" @refresh="fetchData">
        <Range @fetch="refreshAll" />
        <template #data="{ rows }">
          <tr v-for="item in rows" :key="item.code">
            <td>
              <span class="fw-medium">
                {{ $datetim(item.created_at) }}
              </span>
            </td>
            <td>{{ item.cash_code }}</td>
            <td>{{ types[item.type] }}</td>
            <td>{{ item.serie }}</td>
            <td>
              <template v-if="item.name.type === 'c'"> Cliente </template>
              <template v-else>
                Estudiante
                <span :mytooltip="fullSection(item.name.type)">
                  <i class="icon ion-md-information-circle icon-sm text-secondary pointer"></i>
                </span>
              </template>
            </td>
            <td>
              <b>{{ item.name.name }}</b>
            </td>
            <td>{{ $currency(item.total) }}</td>
            <td class="space">
              <MAction icon="eye" tool="Ver Detalle" @action="showDetail(item)" />
              <MAction icon="print" color="info" tool="Ticket" @action="handlePrint(item)" />
              <MAction
                v-show="isToday(item.created_at)"
                icon="trash"
                color="danger"
                tool="Anular"
                @action="handleOpen(item)"
              />
            </td>
          </tr>
        </template>
        <template #foot>
          <h5 class="text-center my-3">Total de Ingresos: {{ $currency(total) }}</h5>
        </template>
      </MTable>
      <template #foot>
        <Footext :title="`${incomes.length} ingresos registrados`" />
      </template>
    </Card>
    <Dialog id="incomeDetailModal" title="Detalles del Ingreso" size="modal-lg" @cancel="handleClosed">
      <MTable :columns="dColumns" :data="details" :head="false">
        <template #data="{ rows }">
          <tr v-for="(item, index) in rows" :key="index">
            <td>{{ item.actiontype.name }}</td>
            <td>{{ item.title }}</td>
            <td>{{ $currency(item.topay) }}</td>
            <td>{{ $currency(item.discount) }}</td>
            <td>{{ $currency(item.paid) }}</td>
            <td>
              {{ $currency(item.topay - item.discount - item.paid) }}
            </td>
          </tr>
        </template>
      </MTable>
      <template #foot>
        <Footext :title="`Correlativo: ${selected.serie}`" />
      </template>
    </Dialog>
    <Cancel :serie="selected.serie" @save="handleCancel" />
  </section>
</template>

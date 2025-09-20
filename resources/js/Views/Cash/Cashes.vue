<script setup>
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import Month from "../../Components/Views/Month.vue";
import { iso } from "../../core/date.js";
import { dformat } from "../../core/day.js";
import { downl } from "../../core/util";
import { exportToExcelCashApi, fetchByMonthCashApi } from "../../http";
import { useDateStore } from "../../pinia/date";
import { useCasherStore } from "../../pinia/casher";
const columns = ["Dia", "Aperturado por:", "Código", "Saldo Inicial", "Ingresos", "Gastos", "Rendido", "Saldo", "Caja"];
const cashes = ref([]);
const lastCash = ref(0);
const router = useRouter();

const incomes = computed(() => {
  return cashes.value.reduce((acu, item) => {
    return acu + parseFloat(item.isum || 0);
  }, 0);
});

const expenses = computed(() => {
  return cashes.value.reduce((acu, item) => {
    return acu + parseFloat(item.esum || 0);
  }, 0);
});

const surrendered = computed(() => {
  return cashes.value.reduce((acu, item) => {
    if (item.surrendered !== null) {
      return acu + parseFloat(item.surrendered.amount);
    }
    return acu + 0;
  }, 0);
});

const formatedDate = (date) => dformat(date, "dddd DD");

function calculateInitial(item) {
  let sum = Number(item.cash) + Number(item.esum);
  if (item.surrendered !== null) {
    sum += Number(item.surrendered.amount);
  }
  return `S/:${(sum - item.isum).toFixed(2)}`;
}

async function fetchData() {
  const { data } = await fetchByMonthCashApi(useDateStore().month);
  cashes.value = data.values;
  if (data.values.length) {
    lastCash.value = data.values[0].cash;
  }
}

function handleGoList(where, created_at, user_code) {
  const { setRange } = useDateStore();
  const { setUser } = useCasherStore();
  setUser(user_code);
  const to_date = new Date(created_at);
  to_date.setDate(to_date.getDate() + 1);
  setRange({ from: iso(new Date(created_at)), to: iso(to_date) });
  router.push({ name: where });
}
function handleGoCash(date) {
  const { setDate } = useDateStore();
  setDate(iso(new Date(date)));
  router.push({ name: "cash" });
}
async function handleExport() {
  const { data } = await exportToExcelCashApi();
  downl(data, "Resumen de Ingresos y Egresos", ".xlsx");
}
</script>

<template>
  <Card title="Resumen de Ingresos y Egresos">
    <template #rb>
      <MButton
        :disabled="!cashes.length"
        color="btn-warning btn-icon"
        icon="icon ion-md-cloud-download icon-md"
        @ok="handleExport"
      />
    </template>
    <Month @updated="fetchData" />
    <MTable :columns="columns" :fetch="fetchData" :data="cashes" :head="false">
      <template #data="{ rows }">
        <tr v-for="(item, index) in rows" :key="index">
          <td>
            <b>{{ formatedDate(item.created_at) }}</b>
          </td>
          <td>{{ item.user ? item.user.name : "" }}</td>
          <td>{{ item.code }}</td>
          <td>{{ calculateInitial(item) }}</td>
          <td>
            <div
              class="sum space"
              mytooltip="Ver detallado"
              @click="handleGoList('incomes', item.created_at, item.user_code)"
            >
              <i class="bg-secondary"></i>
              <u class="text-success">{{ $currency(item.isum) }}</u>
            </div>
          </td>
          <td>
            <div
              class="sum space"
              mytooltip="Ver detallado"
              @click="handleGoList('expense', item.created_at, item.user_code)"
            >
              <i class="bg-info"></i>
              <u class="text-primary-emphasis">{{ $currency(item.esum) }}</u>
            </div>
          </td>
          <td>
            <div v-if="item.surrendered !== null">
              {{ $currency(item.surrendered.amount) }}
              <span :mytooltip="item.surrendered.recipient">
                <i class="icon ion-md-information-circle icon-sm text-secondary pointer"></i>
              </span>
            </div>
          </td>
          <td>{{ $currency(item.cash) }}</td>
          <td>
            <MAction icon="cart" tool="Caja" color="success" @action="handleGoCash(item.created_at)" />
          </td>
        </tr>
        <tr>
          <td colspan="4"></td>
          <td>
            <b class="text-secondary">Total: </b>
            {{ $currency(incomes) }}
          </td>
          <td>
            <b class="text-secondary">Total: </b>
            {{ $currency(expenses) }}
          </td>
          <td>
            <b class="text-secondary">Total: </b>
            {{ $currency(surrendered) }}
          </td>
          <td colspan="2"></td>
        </tr>
      </template>
    </MTable>
    <template #foot>
      <Footext :title="`Hay ${cashes.length} cajas aperturados este mes`" />
    </template>
  </Card>
</template>

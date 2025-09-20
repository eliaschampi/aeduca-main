<script setup>
import { ref } from "vue";
import { fetchCanceledsApi } from "../../http";
import { useUserStore } from "../../pinia/user";
const { fullyear } = useUserStore();
const incomes = ref([]);
const types = {
  "00": "Nota de Venta",
  "03": "Boleta de Venta",
};
const columns = ["Fecha", "Comprobante", "Correlativo", "Razon Social", "Total Importe", "Justificacion"];

async function fetchData() {
  const { data } = await fetchCanceledsApi();
  incomes.value = data.values;
}
</script>

<template>
  <Panel :title="`Comprobantes Anulados ${fullyear}`">
    <MTable :columns="columns" :data="incomes" :fetch="fetchData">
      <template #data="{ rows }">
        <tr v-for="(item, index) in rows" :key="index">
          <td>{{ $datetim(item.created_at) }}</td>
          <td>{{ types[item.type] }}</td>
          <td>{{ item.serie }}</td>
          <td>{{ item.name.name }}</td>
          <td>{{ item.total }}</td>
          <td>
            <template v-if="item.canceled">
              {{ item.canceled.justification }}
            </template>
          </td>
        </tr>
      </template>
    </MTable>
  </Panel>
</template>

<script setup>
import { ref } from "vue";
import Pagination from "../../Components/Ui/Pagination.vue";
import { fetchTracking } from "../../http";

const types = {
  create: "Creo un registro nuevo",
  change: "Actualizó un registro",
  delete: "Eliminó un registro",
};
const pagination = ref({});
const tracks = ref([]);
const columns = ["#", "Usuario", "Acción", "Descripción", "¿Cuando?"];

async function fetchData(page = 1) {
  const {
    data: { values },
  } = await fetchTracking(page);
  tracks.value = values.data;
  pagination.value = {
    ...values,
    data: undefined,
  };
}
</script>

<template>
  <Card title="Reporte de actividades importantes">
    <MTable :columns="columns" :data="tracks" :fetch="fetchData">
      <template #data="{ rows }">
        <tr v-for="item in rows" :key="item.code">
          <td>{{ item.code }}</td>
          <td>
            <b>{{ item.user.name }}</b>
          </td>
          <td>
            <div class="badge bg-warning-subtle text-warning-emphasis">
              {{ types[item.action] }}
            </div>
          </td>
          <td class="d-flex space">
            <div style="max-width: 12rem">{{ item.description }}</div>
          </td>
          <td>
            <em>{{ $datetim(item.created_at) }}</em>
          </td>
        </tr>
      </template>
      <template #foot>
        <Pagination :data="pagination" @pagination-change-page="fetchData" />
      </template>
    </MTable>
    <template #foot>
      <Footext :title="`${tracks.length} registros`" />
    </template>
  </Card>
</template>

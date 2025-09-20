<script setup>
import { ref } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import { fetchAllCrApi } from "../../http";
const customers = ref([]);
const customer = ref({});
const emit = defineEmits(["selected"]);
async function fetchData() {
  const { data } = await fetchAllCrApi();
  customers.value = data.values;
}
</script>

<template>
  <Dialog
    id="customerModal"
    title="Clientes Frecuentes"
    btn-name="Seleccionar"
    @open="fetchData"
    @ok="emit('selected', customer)"
  >
    <MSelect v-model="customer" name="customer" label="Elija un Cliente" :options="customers" opt-value="all" />
  </Dialog>
</template>

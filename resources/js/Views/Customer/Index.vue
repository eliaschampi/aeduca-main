<script setup>
import { useForm } from "vee-validate";
import { onMounted, reactive, ref } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import { delCrApi, fetchAllCrApi, storeCrApi, updateCrApi } from "../../http";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
const { successSnack, showSnack } = useSnackbarStore();
const { showModal, hideModal } = useModalStore();
const customers = ref([]);
const columns = ["Código", "Nombre o Razon Social", "N° telf.", "Acciones"];

const customer = reactive({
  name: "",
  contact_number: "",
});

const { resetForm, handleSubmit } = useForm({
  validationSchema: {
    name: {
      required: true,
    },
    contact_number: {
      max: 30,
    },
  },
});

async function fetchData() {
  const { data } = await fetchAllCrApi();
  customers.value = data.values;
}

function handleShowModal(item) {
  Object.assign(customer, item);
  showModal("customerModal");
}

function handleDeleteClick(item) {
  showSnack({
    text: "¿Está seguro de eliminar el cliente?",
    button: "Confirmar",
    action: async () => {
      const { data } = await delCrApi(item.code);
      successSnack(data.message);
      customers.value.splice(customers.value.indexOf(item), 1);
    },
  });
}

function handleCancel() {
  resetForm({ errors: {} });
  customer.code = undefined;
}

function store() {
  if (customer.code) {
    return updateCrApi(customer);
  }
  return storeCrApi(customer);
}

const onSubmit = handleSubmit(async () => {
  const { data } = await store();
  successSnack(data.message);
  hideModal("customerModal");
  fetchData();
});

onMounted(fetchData);
</script>

<template>
  <section id="customer">
    <Card title="Registro de Clientes">
      <template #rb>
        <MButton
          data-bs-toggle="modal"
          data-bs-target="#customerModal"
          color="btn-inverse-info"
          class="btn-icon"
          icon="icon ion-md-add"
        />
      </template>
      <MTable :columns="columns" :data="customers" :head="false" @refresh="fetchData">
        <template #data="{ rows }">
          <tr v-for="item in rows" :key="item.code">
            <td>{{ item.code }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.contact_number }}</td>
            <td class="space">
              <MAction @action="handleShowModal(item)" />
              <MAction icon="trash" color="danger" tool="Eliminar" @action="handleDeleteClick(item)" />
            </td>
          </tr>
        </template>
      </MTable>
      <template #foot>
        <Footext :title="`${customers.length} clientes`" />
      </template>
    </Card>
    <Dialog
      id="customerModal"
      btn-name="Guardar"
      :title="customer.code ? 'Editar Cliente' : 'Nuevo Cliente'"
      @ok="onSubmit"
      @cancel="handleCancel"
    >
      <div class="row gx-2">
        <MInput v-model="customer.name" name="name" class="col-md-6" label="Nombre o Razon Social" />
        <MInput v-model="customer.contact_number" name="contact_number" class="col-md-6" label="Telf de Contacto" />
      </div>
    </Dialog>
  </section>
</template>

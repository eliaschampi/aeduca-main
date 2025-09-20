<script setup>
import { useForm } from "vee-validate";
import { computed, reactive, ref } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import { fetchCatApi, storeCatApi, updateCatApi } from "../../http";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
const { showModal, hideModal } = useModalStore();
const { successSnack } = useSnackbarStore();
const cats = ref([]);
const mode = ref("Ingreso");

const cat = reactive({
  name: "",
});

const schema = {
  name: {
    required: true,
    max: 50,
  },
};

const columns = ["Código", "Descripción", "Acciones"];

const { resetForm, handleSubmit } = useForm({
  validationSchema: schema,
});

const isUpdate = computed(() => {
  return !cat.code === false;
});

async function fetchData() {
  const { data } = await fetchCatApi(mode.value);
  cats.value = data.values;
}

async function storeData() {
  if (!isUpdate.value) {
    return storeCatApi({
      ...cat,
      mode: mode.value,
    });
  }
  return updateCatApi(cat);
}

function handleShowModal(item) {
  Object.assign(cat, item);
  showModal("typeModal");
}

function handleCancel() {
  resetForm({ errors: {} });
  cat.code = undefined;
}

const onSubmit = handleSubmit(async () => {
  const { data } = await storeData();
  hideModal("typeModal");
  successSnack(data.message);
  fetchData();
});

const modalOptions = computed(() => {
  return {
    title: isUpdate.value ? "Editar" : "Crear",
    btnName: isUpdate.value ? "Actualizar" : "Crear",
  };
});
</script>

<template>
  <section>
    <Card title="Tipos de ingresos y gastos">
      <template #rb>
        <MButton
          data-bs-toggle="modal"
          data-bs-target="#typeModal"
          color="btn-inverse-info"
          class="btn-icon"
          icon="icon ion-md-add"
        />
      </template>
      <MTable :columns="columns" :data="cats" :fetch="fetchData">
        <MSelect
          v-model="mode"
          name="modality"
          style="max-width: 10rem"
          standalone
          :options="['Ingreso', 'Egreso']"
          @update:model-value="fetchData"
        />
        <template #data="{ rows }">
          <tr v-for="item in rows" :key="item.code">
            <td>{{ item.code }}</td>
            <td>{{ item.name }}</td>
            <td>
              <MAction @action="handleShowModal(item)" />
            </td>
          </tr>
        </template>
      </MTable>
      <template #foot>
        <Footext :title="`${cats.length} registros`" />
      </template>
    </Card>
    <Dialog
      id="typeModal"
      :btn-name="modalOptions.btnName"
      :title="modalOptions.title"
      @ok="onSubmit"
      @cancel="handleCancel"
    >
      <MInput v-model="cat.name" name="name" label="Descripción" placeholder="Descripción del tipo de ingreso" />
    </Dialog>
  </section>
</template>

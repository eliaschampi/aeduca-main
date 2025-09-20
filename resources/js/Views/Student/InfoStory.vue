<script setup>
import AddInfo from "./components/AddInfo.vue";
import { computed, ref, watchEffect } from "vue";
import {
  delExtraApi,
  downloadAExtraApi,
  fetchByStExtraApi,
  storeExtraApi,
  updateExtraApi,
  printInfoStApi,
} from "../../http";
import { useModalStore } from "../../pinia/modal";
import { downl } from "../../core/util";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useStudentStore } from "../../pinia/student";
const storedDni = computed(() => useStudentStore().dni);
const { successSnack, warningSnack } = useSnackbarStore();
const { hideModal } = useModalStore();
const infoModal = ref();
const idata = ref([]);
const isLoading = ref(false);
const showModal = () => {
  infoModal.value.handleShowModal();
};

const editInfo = (payload) => {
  infoModal.value.handleShowModal(payload);
};

const handleSaveClick = async (p, file, callback) => {
  const store = () => {
    if (!p.code) {
      p["student_dni"] = storedDni.value;
      const formData = new FormData();
      formData.append("data", JSON.stringify(p));
      formData.append("file", file);
      return storeExtraApi(formData);
    } else {
      return updateExtraApi(p);
    }
  };

  if (idata.value.some(({ code, type, info }) => code !== p.code && type === p.type && info === p.info)) {
    warningSnack("Posible duplicado");
    return;
  }
  const { data } = await store();
  callback();
  successSnack(data.message);
  fetchData(storedDni.value);
};

const fetchData = async (student_dni) => {
  const { data } = await fetchByStExtraApi(student_dni);
  idata.value = data.values;
};

const handleDeleteClick = async (item) => {
  const { data } = await delExtraApi(item.code);
  successSnack(data.message);
  idata.value.splice(idata.value.indexOf(item), 1);
};

const handleDownloadAttached = async ({ code, attached }) => {
  const { data } = await downloadAExtraApi(code);
  successSnack("El documento se descargará en un momento");
  downl(data, attached, "");
  hideModal("newInfo");
};

async function handlePrint() {
  if (isLoading.value) return;
  isLoading.value = true;
  const { data } = await printInfoStApi(storedDni.value);
  downl(data, `Estudiante ${useStudentStore().fullname}`, ".zip");
  isLoading.value = false;
}

watchEffect(() => {
  fetchData(storedDni.value);
});
</script>

<template>
  <div class="d-flex space">
    <MButton v-can="'AS'" v-year icon="icon ion-md-add" size="btn-sm" color="btn-inverse-primary" @ok="showModal">
      Agregar
    </MButton>
    <MButton
      icon="icon ion-md-cloud-download"
      color="btn-inverse-info"
      size="btn-sm"
      :disabled="isLoading"
      @ok="handlePrint"
    >
      Ficha Unica
    </MButton>
  </div>
  <MTable :columns="['#', 'Tipo', 'Descripción', 'Acciones']" :data="idata" :head="false" :is-loading="isLoading">
    <template #data="{ rows }">
      <tr v-for="item in rows" :key="item.code">
        <td><span class="icon ion-md-folder-open"></span></td>
        <td style="max-width: 5rem">{{ item.type }}</td>
        <td style="max-width: 15rem">{{ item.info }}</td>
        <td v-can="'AS'" class="space">
          <MAction @action="editInfo(item)" />
          <MAction icon="trash" color="danger" tool="Eliminar" @action="handleDeleteClick(item)" />
        </td>
      </tr>
    </template>
  </MTable>
  <AddInfo ref="infoModal" @save="handleSaveClick" @download-a="handleDownloadAttached" />
</template>

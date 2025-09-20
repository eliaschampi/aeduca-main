<script setup>
import { ref } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
import File from "../../../Components/Ui/File.vue";
import { uploadConfigApi } from "../../../http";
import { useModalStore } from "../../../pinia/modal";
import { useSnackbarStore } from "../../../pinia/snackbar";
const type = ref("card");
const image = ref(null);
const { successSnack } = useSnackbarStore();
const { showModal, hideModal } = useModalStore();

function handleShowModal(templateType) {
  type.value = templateType;
  showModal("uploadBGModal");
}

async function handleUpload() {
  const formData = new FormData();
  formData.set("image", image.value);
  formData.append("name", type.value);
  const { data } = await uploadConfigApi(formData);
  successSnack(data.message);
  hideModal("uploadBGModal");
}

defineExpose({
  handleShowModal,
});
</script>

<template>
  <Dialog id="uploadBGModal" title="Subir una nueva plantilla" :disabled="!image" btn-name="Subir" @ok="handleUpload">
    <Alert type="alert-danger">
      Al actualizar la plantilla o el membrete ({{ type }}), este, afectará tambien a todas las sedes de la institución
    </Alert>
    <div class="d-flex justify-content-center">
      <File v-model="image" :size="260" :allowed-types="['image/png']" />
    </div>
  </Dialog>
</template>

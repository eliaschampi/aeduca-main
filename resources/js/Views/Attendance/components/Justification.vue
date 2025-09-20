<script setup>
import { reactive, ref } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
import File from "../../../Components/Ui/File.vue";
import { downl } from "../../../core/util";
import { downloadAttachedJsApi, storeJsApi, toggleJsApi } from "../../../http";
import { useModalStore } from "../../../pinia/modal";
import { useSnackbarStore } from "../../../pinia/snackbar";
const { successSnack } = useSnackbarStore();
const { showModal, hideModal } = useModalStore();
const emit = defineEmits(["done"]);
const justification = reactive({ description: "", attached: null });
const code = ref(null);
const approve = ref("no");
const state = ref("");
const isNew = ref(false);

async function handleDownloadAttached() {
  const { data } = await downloadAttachedJsApi(code.value);
  downl(data, justification.attached, "");
}

const handleAbsenceStore = async () => {
  const formData = new FormData();
  formData.append("code", code.value);
  formData.append("description", justification.description);
  formData.append("file", justification.attached);
  const { data } = await storeJsApi(formData);
  successSnack(data.message);
  emit("done");
};

const handleToggleJs = async () => {
  const { data } = await toggleJsApi(code.value, approve.value);
  successSnack(data.message);
  emit("done");
};

function handleSubmitClick() {
  if (state.value === "envió justificación") {
    handleToggleJs();
  } else if (state.value === "falta") {
    handleAbsenceStore();
  }
  hideModal("jusModal");
}

function handleShowModal($code, $state, $justification) {
  code.value = $code;
  state.value = $state;
  if ($justification) {
    isNew.value = false;
    justification.description = $justification.description;
    justification.attached = $justification.attached;
  } else {
    isNew.value = true;
  }
  showModal("jusModal");
}

defineExpose({
  handleShowModal,
});
</script>

<template>
  <Dialog
    id="jusModal"
    title="Justificación"
    btn-name="Actualizar"
    :disabled="!justification.description"
    @ok="handleSubmitClick"
  >
    <div class="space-b-1">
      <MTextarea v-model="justification.description" name="description" label="Justificación" :disabled="!isNew" />
      <div v-if="!isNew" class="text-center my-2">
        <MButton
          v-show="justification.attached"
          color="btn-warning"
          icon="icon ion-md-cloud-download"
          size="btn-sm"
          @ok="handleDownloadAttached"
        >
          Descargar archivo adjunto
        </MButton>
        <div v-show="state === 'envió justificación'" class="selectgroup selectgroup-pills">
          <label class="selectgroup-item">
            <input v-model="approve" class="selectgroup-input" name="accept_jus" type="radio" value="yes" />
            <span class="selectgroup-button">Aceptar</span>
          </label>
          <label class="selectgroup-item">
            <input v-model="approve" class="selectgroup-input" name="accept_jus" type="radio" value="no" />
            <span class="selectgroup-button">Rechazar</span>
          </label>
        </div>
      </div>
      <div v-else class="d-flex justify-content-center">
        <File v-model="justification.attached" />
      </div>
    </div>
  </Dialog>
</template>

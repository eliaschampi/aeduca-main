<script setup>
import { computed, ref } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
import { useModalStore } from "../../../pinia/modal";
const { showModal, hideModal } = useModalStore();
const advance = ref("");

const checktext = computed(() => advance.value === "Acepto");

const emit = defineEmits(["okdelete"]);

function handleShowModal(mode = "show") {
  advance.value = "";
  if (mode === "show") {
    showModal("dpm");
  } else {
    hideModal("dpm");
  }
}

defineExpose({ handleShowModal });
</script>

<template>
  <Dialog id="dpm" title="Eliminar permanentemente" btn-name="Eliminar" :disabled="!checktext" @ok="emit('okdelete')">
    <Alert> Toda la información relacionada será eliminada </Alert>
    <div class="form-floating">
      <input id="finderid" v-model="advance" class="form-control" placeholder="Acepto" />
      <label class="form-label" for="finderid"> Para continuar escriba la palabra <b>Acepto</b> </label>
    </div>
  </Dialog>
</template>

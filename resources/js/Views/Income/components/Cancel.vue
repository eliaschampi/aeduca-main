<script setup>
import { useForm } from "vee-validate";
import { reactive } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
const emit = defineEmits(["save"]);
const props = defineProps({
  serie: String,
});

const cancel = reactive({
  justification: "",
});

const { handleSubmit } = useForm({
  validationSchema: {
    justification: {
      required: true,
      min: 5,
      max: 50,
    },
  },
});

function handleSave({ justification }) {
  emit("save", justification);
}

const onSubmit = handleSubmit(handleSave);
</script>

<template>
  <Dialog id="cancel" :title="'Anulando el comprobante: ' + props.serie" btn-name="Anular" @ok="onSubmit">
    <MInput v-model="cancel.justification" name="justification" label="¿Por qué anula este Comprobante?" />
  </Dialog>
</template>

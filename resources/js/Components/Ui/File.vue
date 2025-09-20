<script setup>
import { computed } from "vue";
import { useSnackbarStore } from "../../pinia/snackbar";
const { warningSnack } = useSnackbarStore();
const props = defineProps({
  modelValue: [String, File],
  size: {
    type: Number,
    default: 4 * 1024,
  },
  allowedTypes: {
    type: Array,
    default: () => ["application/pdf", "image/jpeg", "image/png"],
  },
});
const emit = defineEmits(["update:modelValue"]);
const maxsize = computed(() => props.size * 1024);
const filename = computed(() => {
  if (props.modelValue) {
    if (typeof props.modelValue === "string") {
      return props.modelValue;
    }
    return props.modelValue.name;
  }
  return "";
});

const handleUploadFile = (e) => {
  const file = e.target.files[0];

  if (!props.allowedTypes.includes(file.type)) {
    warningSnack("Formato no admitido, intente con otro archivo");
    emit("update:modelValue", null);
    return;
  }

  if (file.size > maxsize.value) {
    warningSnack("Peso del archivo excede el límite");
    emit("update:modelValue", null);
    return;
  }

  emit("update:modelValue", file);
};

const handleClear = () => {
  emit("update:modelValue", null);
};
</script>

<template>
  <div class="uploader">
    <div class="fw-medium mb-2">Documento adjunto (pdf o imagen)</div>
    <label class="border-rounded p-2 text-center pointer d-block" for="attachedId">
      <u class="fw-medium">
        {{ filename }}
      </u>
      <input
        id="attachedId"
        class="d-none"
        name="file"
        type="file"
        :accept="allowedTypes.join(',')"
        @change="handleUploadFile"
      />
      <i v-if="!modelValue" class="d-block icon ion-md-cloud-upload text-title icon-lg"></i>
      <i v-else class="d-block icon ion-md-checkmark-circle text-success icon-lg"></i>
    </label>
    <em v-if="!modelValue" class="small text-warning-emphasis"> Tamaño maximo {{ (maxsize / 1024).toFixed(2) }} kb </em>
    <i v-else class="small text-danger pointer icon ion-md-trash" @click="handleClear"> Eliminar</i>
  </div>
</template>

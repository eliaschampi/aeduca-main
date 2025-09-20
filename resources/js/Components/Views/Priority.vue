<script setup>
import { usePriorityStore } from "../../pinia/priority";
import { useSnackbarStore } from "../../pinia/snackbar";
const store = usePriorityStore();
const { successSnack } = useSnackbarStore();
const emit = defineEmits(["updated"]);
defineProps({
  isSm: {
    type: Boolean,
    default: true,
  },
});
const opt = {
  1: {
    icon: "ion-md-sunny",
    color: "btn-inverse-warning",
  },
  2: {
    icon: "ion-md-moon",
    color: "btn-inverse-danger",
  },
};

const handleUpdate = () => {
  store.update();
  successSnack(`Turno cambiado a ${opt[store.priority].label}`);
  emit("updated");
};
</script>

<template>
  <MButton
    :icon="opt[store.priority].icon"
    :color="opt[store.priority].color"
    :class="{ 'btn-sm': isSm }"
    class="btn-icon"
    @ok="handleUpdate"
  />
</template>

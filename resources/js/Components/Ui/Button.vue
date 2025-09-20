<script setup>
const emit = defineEmits(["ok"]);
defineProps({
  color: {
    type: String,
    default: "btn-inverse-success",
  },
  size: {
    type: String,
    default: "btn-md",
  },
  icon: {
    type: String,
    default: "",
  },
});

function handleSave() {
  emit("ok");
}
</script>

<template>
  <button type="button" class="btn" :class="[color, size]" @click="handleSave">
    <i v-show="icon" :class="icon"></i>
    <slot></slot>
  </button>
</template>

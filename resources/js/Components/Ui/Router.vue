<script setup>
const props = defineProps({
  color: {
    type: String,
    default: "btn-info",
  },
  size: {
    type: String,
    default: "btn-md",
  },
  icon: {
    type: String,
    default: "",
  },
  to: [Object, String],
});
</script>

<template>
  <RouterLink class="btn" :class="[props.color, props.size]" :to="props.to">
    <i v-show="props.icon" :class="props.icon"></i>
    <slot></slot>
  </RouterLink>
</template>

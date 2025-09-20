<script setup>
const props = defineProps({
  title: {
    type: String,
    default: "",
  },
  icon: {
    type: String,
    default: "ion-md-folder",
  },
  f: {
    type: Boolean,
    default: true,
  },
});
</script>

<template>
  <div class="card head-panel">
    <div class="card-header">
      <div class="float-end card-action">
        <slot name="rb"></slot>
      </div>
      <div class="card-title">
        <i class="text-info ion" :class="props.icon"></i>
        {{ props.title }}
      </div>
      <slot name="subtitle"></slot>
    </div>
    <div class="card-body">
      <slot></slot>
    </div>
    <div v-if="props.f" class="card-footer">
      <slot name="foot"></slot>
    </div>
  </div>
</template>

<script setup>
import { useDateStore } from "../../../pinia/date";
const emit = defineEmits(["golist"]);
const { isToday } = useDateStore();
defineProps({
  title: String,
  icon: String,
  content: String,
  count: Number,
  gooAdd: String,
});
</script>

<template>
  <Panel class="flex-fill mb-2" :title="title" f style="max-width: 18rem">
    <h2 class="font-weight-light">
      <i :class="icon"></i>
      {{ $currency(content) }}
    </h2>
    <div>
      <span class="fw-medium"> N° de {{ title }}: </span>
      <div class="badge bg-success-subtle text-success-emphasis">
        {{ count }}
      </div>
    </div>
    <template #foot>
      <div class="space">
        <MRouter v-show="isToday" size="btn-sm" color="btn-inverse-primary" :to="{ name: gooAdd }"> Agregar </MRouter>
        <MButton size="btn-sm" color="btn-inverse-info" @ok="emit('golist')"> Ver {{ title }} </MButton>
      </div>
    </template>
  </Panel>
</template>

<script setup>
import months from "../../data/months.json";
import { useDateStore } from "../../pinia/date";
const emit = defineEmits(["updated"]);
const store = useDateStore();
</script>

<template>
  <MSelect
    v-model="store.month"
    name="monthname"
    style="max-width: 15rem"
    standalone
    :options="months"
    placeholder="Mes"
    @update:model-value="emit('updated')"
  />
</template>

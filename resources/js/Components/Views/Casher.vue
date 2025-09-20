<script setup>
import { useCasherStore } from "../../pinia/casher";
const store = useCasherStore();
const emit = defineEmits(["changed"]);
const classes = (code) => {
  if (code === store.user_code) {
    return "bg-primary";
  }
  return "bg-primary-subtle text-primary-emphasis";
};

const updateUserCode = (user_code) => {
  if (user_code === store.user_code) return;
  store.$patch({
    user_code,
  });
  emit("changed");
};
</script>

<template>
  <div class="d-flex space overflow-auto">
    <template v-for="item in store.cashers" :key="item.code">
      <div class="badge pointer" :class="classes(item.code)" @click="updateUserCode(item.code)">
        {{ item.name }}
      </div>
    </template>
  </div>
</template>

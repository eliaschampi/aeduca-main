import { defineStore } from "pinia";
import { ref } from "vue";

export const usePriorityStore = defineStore("priority", () => {
  const priority = ref(1);

  const setPriority = () => {
    const hour = new Date().getHours();
    priority.value = hour < 12 ? 1 : 2;
  };

  setPriority();

  function update() {
    priority.value = priority.value === 1 ? 2 : 1;
  }

  return { priority, update };
});

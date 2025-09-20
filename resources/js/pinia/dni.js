import { defineStore } from "pinia";

export const useDNIStore = defineStore("dni", {
  state: () => ({
    dni: "",
  }),
});

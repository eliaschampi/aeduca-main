import { defineStore } from "pinia";
import { fetchCatApi } from "../http";

export const useCatStore = defineStore("cat", {
  state: () => ({
    cats: [],
  }),
  actions: {
    async fetchCats() {
      if (this.cats.length === 0) {
        const { data } = await fetchCatApi("Ingreso");
        this.cats = data.values;
      }
    },
  },
});

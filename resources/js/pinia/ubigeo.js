import { defineStore } from "pinia";
import { ref } from "vue";
import cache from "../core/cache";
import { fetchUbiApi } from "../http";

export const useUbigeoStore = defineStore("ubigeo", () => {
  const ubigeo = ref([]);

  async function fetchUbigeo() {
    if (ubigeo.value.length !== 0) return;
    const ubi = cache.getItem("ubigeo");
    if (ubi) {
      ubigeo.value = ubi;
    } else {
      const {
        data: { values },
      } = await fetchUbiApi();
      cache.setItem("ubigeo", values);
      ubigeo.value = values;
    }
  }
  return {
    ubigeo,
    fetchUbigeo,
  };
});

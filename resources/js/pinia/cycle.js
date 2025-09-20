import { defineStore } from "pinia";
import { fetchAllCyApi } from "../http";
import { iso } from "../core/date";

export const useCycleStore = defineStore("cycle", {
  state: () => ({
    cycles: [],
    current: "",
    isLoading: false,
    show_overdues: false,
  }),
  actions: {
    toggleOverdues(state) {
      this.show_overdues = state;
    },
    async fetchCycles(cycle_code = "") {
      this.isLoading = true;
      if (this.cycles.length > 0) return;
      const {
        data: { values: cycles },
      } = await fetchAllCyApi();
      this.setCycles(cycles);
      if (this.current === "" && cycles.length > 0) {
        if (cycle_code === "") {
          cycle_code = cycles[0].code;
        }
        this.current = cycle_code;
      }
      this.isLoading = false;
    },
    setCycles(cycles = []) {
      this.cycles = cycles;
      if (cycles.length === 0) {
        this.current = "";
      }
    },
    setCurrent(current) {
      this.current = current;
    },
    onSuccessFinalize(code) {
      const cycle = this.cycles.find((item) => item.code === code);
      if (cycle) {
        cycle.to = iso();
      }
    },
  },
  getters: {
    actives({ cycles }) {
      return cycles.filter((item) => {
        const to = new Date(item.to).valueOf();
        return to > new Date();
      });
    },

    overdues({ cycles }) {
      return cycles.filter((item) => {
        const to = new Date(item.to).valueOf();
        return to <= new Date();
      });
    },
  },
});

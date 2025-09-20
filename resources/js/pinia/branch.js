import { defineStore } from "pinia";
import { fetchAllBranchApi } from "../http";

export const useBranchStore = defineStore("branch", {
  state: () => ({
    branches: [],
    sfmOps: {
      who: "family",
      mode: 1,
      onlyCurrentReg: true,
      onlyCurrentBranch: true,
    },
  }),
  actions: {
    async fetchBranches() {
      if (this.branches.length === 0) {
        const { data } = await fetchAllBranchApi();
        this.branches = data.values;
      }
    },
    updateSfmOption({ onlyCurrentReg = true, onlyCurrentBranch = true, mode, who, after }) {
      if (who === "student" && onlyCurrentBranch === false) {
        this.fetchBranches();
      }
      this.sfmOps = { onlyCurrentReg, onlyCurrentBranch, mode, who };
      after();
    },
  },
  getters: {
    branchesForFinder(state) {
      if (!state.sfmOps.onlyCurrentBranch && state.sfmOps.who === "student") {
        return state.branches;
      }
      return [];
    },
  },
});

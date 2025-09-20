import { defineStore } from "pinia";
import { fetchAllDgApi, fetchDgApi } from "../http";
import { useCycleStore } from "./cycle";

export const useDegreeStore = defineStore("degree", {
    state: () => ({
        degrees: [],
        degree: {},
    }),
    actions: {
        async fetchDegrees() {
            const { current } = useCycleStore();
            const { data } = await fetchAllDgApi(current);
            this.degrees = data.values;
        },
        async fetchDegree(code) {
            const { data } = await fetchDgApi(code);
            this.degree = data.value;
        },
        increaseSectionCount() {
            this.degree.sections_count++;
        },
    },
    getters: {
        exists: (state) => {
            return Object.keys(state.degree).length > 0;
        },
        code: (state) => state.degree.code,
        sections_count: (state) => state.degree.sections_count,
    },
});

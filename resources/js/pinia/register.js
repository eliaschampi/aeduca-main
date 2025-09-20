import { defineStore } from "pinia";
import { fetchBySectionRegApi, fetchLatestRegApi } from "../http";
import { useSectionStore } from "./section";

export const useRegisterStore = defineStore("register", {
    state: () => ({
        registers: [],
        register: null,
        isLoading: false,
    }),
    actions: {
        setRegister(register) {
            this.register = register;
        },
        async fetchBySection(inactives = false) {
            this.isLoading = true;
            const { section_code } = useSectionStore();
            const { data } = await fetchBySectionRegApi(section_code, inactives);
            this.registers = data.values;
            this.isLoading = false;
        },
        async fetchRegister(dni) {
            const { data } = await fetchLatestRegApi(dni);
            this.setRegister(data.value);
            return data.value;
        },
    },
});

import { defineStore } from "pinia";

export const useDNIStore = defineStore({
    id: "dni",
    state: () => ({
        dni: "",
    }),
});

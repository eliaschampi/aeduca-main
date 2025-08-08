import { defineStore } from "pinia";
export const useAppStore = defineStore({
    id: "app",
    state: () => ({
        myapp: window.Laravel.app,
        version: window.Laravel.version,
    }),
});

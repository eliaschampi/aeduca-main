import { defineStore } from "pinia";
export const useAppStore = defineStore("app", {
    state: () => ({
        myapp: window.Laravel.app,
        version: window.Laravel.version,
    }),
});

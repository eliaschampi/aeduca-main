import { defineStore } from "pinia";

export const useFamilyStore = defineStore("family", {
    state: () => ({
        family: null,
    }),
    actions: {
        setPerson(family) {
            this.family = family;
        },
    },
    getters: {
        image: () => {
            return "/default/group.png";
        },
        fullname: ({ family }) => {
            if (family) {
                return `${family.name} ${family.lastname}`;
            }
            return "Buscar apoderado";
        },
        dni: ({ family }) => family?.dni,
    },
});

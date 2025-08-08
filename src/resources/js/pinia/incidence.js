import { defineStore } from "pinia";

const defaultIncidence = {
    title: "",
    type: "in",
    description: "",
    agreement: "",
    extra: "",
    state: true,
    persons: [],
    created_at: new Date(),
};

export const useIncidenceStore = defineStore({
    id: "incidence",
    state: () => ({
        incidence: { ...defaultIncidence },
    }),
    actions: {
        setIncidence(incidence = defaultIncidence) {
            Object.assign(this.incidence, incidence);
        },
    },
});

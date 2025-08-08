import { defineStore } from "pinia";
import { fetchByDegreeScApi } from "../http";

export const useSectionStore = defineStore({
    id: "section",
    state: () => ({
        sections: [],
        section_code: null,
    }),
    actions: {
        async fetchSections(degree_code) {
            const { data } = await fetchByDegreeScApi(degree_code);
            const sections = data.values;
            this.sections = sections;
            if (sections.length > 0) {
                const { code } = sections[0];
                if (!this.section_code || this.section_code.substr(0, 9) !== degree_code) {
                    this.setSectionCode(code);
                }
            } else {
                this.setSectionCode(null);
            }
        },
        setSectionCode(code) {
            this.section_code = code;
        },
    },
    getters: {
        code: (state) => state.section_code,
    },
});

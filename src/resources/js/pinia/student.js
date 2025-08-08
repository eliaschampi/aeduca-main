import { defineStore } from "pinia";
import { imagePerson } from "../core/util";

export const useStudentStore = defineStore({
    id: "student",
    state: () => ({
        student: null,
    }),
    actions: {
        setPerson(student) {
            this.student = student;
        },
        updateProfile(profile) {
            this.student.profile = profile;
        },

        updateImage(image) {
            this.student.profile.image = image;
        },
    },
    getters: {
        image: ({ student }) => {
            const { profile, gender } = student || {};
            const img = profile ? profile.image : null;
            return imagePerson(img, gender);
        },
        fullname: ({ student }) => {
            if (student) {
                return `${student.name} ${student.lastname}`;
            }
            return "Buscar estudiante";
        },
        dni: ({ student }) => student?.dni,
        hasProfile: ({ student }) => {
            if (!student) return false;
            if (!student.profile) return false;
            return true;
        },
    },
});

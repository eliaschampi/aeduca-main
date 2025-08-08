import { defineStore } from "pinia";
import { imagePerson } from "../core/util";

export const useTeacherStore = defineStore({
    id: "teacher",
    state: () => ({
        teacher: null,
    }),
    actions: {
        setPerson(teacher) {
            this.teacher = teacher;
        },
        updateProfile(profile) {
            this.teacher.profile = profile;
        },

        updateImage(image) {
            this.teacher.profile.image = image;
        },
    },
    getters: {
        image: ({ teacher }) => {
            const { profile, gender } = teacher || {};
            const img = profile ? profile.image : null;
            return imagePerson(img, gender);
        },
        fullname: ({ teacher }) => {
            if (teacher) {
                return `${teacher.name} ${teacher.lastname}`;
            }
            return "Buscar docente";
        },
        dni: ({ teacher }) => teacher?.dni,
        hasProfile: ({ teacher }) => {
            if (!teacher) return false;
            if (!teacher.profile) return false;
            return true;
        },
    },
});

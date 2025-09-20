import { computed } from "vue";
import { logoutApi } from "../http";
import { useFamilyStore } from "../pinia/family";
import { useSnackbarStore } from "../pinia/snackbar";
import { useStudentStore } from "../pinia/student";
import { useTeacherStore } from "../pinia/teacher";
import { useUserStore } from "../pinia/user";

export function usePlaceholder(who) {
  const arr = {
    student: "estudiante",
    teacher: "docente",
    family: "apoderado",
  };
  return `Buscar ${arr[who]}`;
}

export const useLogout = () => {
  const { can, limited } = useUserStore();
  const showCash = computed(() => can("AS") && !limited);
  const { showSnack } = useSnackbarStore();
  function logOutAndReload() {
    showSnack({
      text: "Estas a punto de salir del sistema",
      button: "Salir Ahora",
      action: logoutApi,
    });
  }

  return {
    showCash,
    logOutAndReload,
  };
};

export const useEntityStore = () => {
  const storeMap = {
    student: useStudentStore,
    teacher: useTeacherStore,
    family: useFamilyStore,
    user: useUserStore,
  };
  const getStore = (entityType) => {
    return storeMap[entityType]();
  };

  return {
    getStore,
  };
};

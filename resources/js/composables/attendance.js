import { onMounted, reactive, ref } from "vue";
import { fetchConfigApi, updateAttApi } from "../http";
import { useSnackbarStore } from "../pinia/snackbar";
const { successSnack } = useSnackbarStore();

export const useAttendanceToolkit = () => {
  const states = {
    presente: "badge bg-success",
    tarde: "badge bg-warning",
    permiso: "badge bg-primary",
    falta: "badge bg-danger",
    "envió justificación": "badge bg-secondary",
    justificado: "badge bg-info",
  };

  const original = ref({});
  const mod = ref(0);

  function handleEdit(item) {
    original.value = { ...item };
    mod.value = item.code;
  }

  function cancelEdit(item) {
    mod.value = 0;
    Object.assign(item, original.value);
    original.value = {};
  }

  async function handleUpdate(item) {
    const { data } = await updateAttApi(item);
    successSnack(data.message);
    mod.value = 0;
    original.value = {};
  }

  return {
    states,
    mod,
    handleEdit,
    cancelEdit,
    handleUpdate,
  };
};

export const useConfigData = () => {
  const configData = reactive({
    entry_time: "",
    tolerance: "",
  });

  async function fetchConfig() {
    const { data } = await fetchConfigApi();
    const { entry_time, tolerance } = data.values;
    configData.entry_time = entry_time;
    configData.tolerance = tolerance;
  }

  onMounted(fetchConfig);

  return {
    configData,
  };
};

<script setup>
import { Form } from "vee-validate";
import { inject, onMounted, onUnmounted, reactive, ref, watch } from "vue";
import { useRouter } from "vue-router";
import IncomeDetail from "../../Components/Views/IncomeDetail.vue";
import { downl } from "../../core/util";
import { fetchAttVarsCyApi, fetchByYearAndBranchApi, hasOnCacheRegApi, setRegApi } from "../../http";
import { useCatStore } from "../../pinia/cat";
import { useDetailStore } from "../../pinia/detail";
import { useModalStore } from "../../pinia/modal";
import { useRegisterStore } from "../../pinia/register";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useStudentStore } from "../../pinia/student";
import { useUserStore } from "../../pinia/user";
const { showModal } = useModalStore();
const { successSnack } = useSnackbarStore();
const emitter = inject("emitter");
const router = useRouter();
const { fullyear } = useUserStore();
const invoicing = ref(false);
const consV = ref(false);
const isLoading = ref(false);
const state = ref(true);
const sections = ref([]);
const prdecorated = {
  1: "Turno mañana",
  2: "Turno tarde",
  3: "Turno noche",
};
const register = reactive({
  monthly: "0.00",
  priority: 0,
  section_code: "",
});
const existsInCache = ref(false);
const mode = ref("Nueva");
const priorities = ref([]);

async function fetchAttVars(cycle_code) {
  const { data } = await fetchAttVarsCyApi(cycle_code);
  priorities.value = data.values;
}

async function fetchSections() {
  const { data } = await fetchByYearAndBranchApi();
  sections.value = data.values;
  if (sections.value.length) {
    const cycle_code = sections.value[0].code.substring(0, 8);
    fetchAttVars(cycle_code);
  }
}

async function checkIfHasCache() {
  const { data } = await hasOnCacheRegApi();
  existsInCache.value = data;
}

function handleSectionChange(value) {
  const cycle_code = value.substring(0, 8);
  const item = sections.value.find((s) => s.code === value);
  register.monthly = item.monthly;
  if (cycle_code !== register.section_code.substring(0, 8)) {
    fetchAttVars(cycle_code);
  }
}
function handleOnStoredSuccessfully(datafromapi, fullname, dni) {
  if (consV.value) {
    downl(datafromapi, `cv_${fullname}`);
  } else {
    successSnack(datafromapi);
  }
  if (invoicing.value) {
    router.push({ name: "invoice" });
    return;
  }
  const { fetchRegister } = useRegisterStore();
  fetchRegister(dni);
  router.push({ name: "student_profile", params: { dni } });
}

async function storeData() {
  const { dni, fullname } = useStudentStore();
  const payload = {
    ...register,
    state: state.value ? "a" : "i",
    mode: mode.value,
    consV: consV.value,
    student_name: fullname,
  };
  if (mode.value === "Modificar") {
    payload.invoicing = false;
  } else {
    if (mode.value === "Nueva") {
      payload.student_dni = dni;
    }
    payload.invoicing = invoicing.value;
  }
  const { data } = await setRegApi(payload);
  handleOnStoredSuccessfully(data, fullname, dni);
  isLoading.value = false;
}

async function handleSubmitClick() {
  isLoading.value = true;
  if (invoicing.value) {
    const type_code = !consV.value ? 300 : 100;
    const { setDetail, setIsNew } = useDetailStore();
    const { cats } = useCatStore();
    const actiontype = cats.find((item) => item.code === type_code);
    setDetail({
      actiontype,
      topay: register.monthly,
      paid: register.monthly,
      month: "",
    });
    setIsNew(false);
    showModal("invoiceModal");
  } else {
    storeData();
  }
}

function fetchData(payload) {
  consV.value = false;
  if (payload.state === "f" || parseInt(payload.year) !== new Date().getFullYear()) {
    Object.assign(register, {
      monthly: "0.00",
      priority: 0,
    });
    mode.value = "Nueva";
  } else {
    Object.assign(register, payload);
    if (payload.state === "p") {
      invoicing.value = true;
      mode.value = "Ratificar";
    } else {
      invoicing.value = false;
      mode.value = "Modificar";
    }
  }
}

watch(invoicing, async (value) => {
  if (value) {
    const { fetchCats } = useCatStore();
    await fetchCats();
  }
});

onMounted(() => {
  emitter.on("registerFetched", fetchData);
  const { register: stored } = useRegisterStore();
  if (stored) {
    fetchData(stored);
  }
  fetchSections();
  checkIfHasCache();
});

onUnmounted(() => {
  emitter.off("registerFetched", fetchData);
});

const schema = {
  section_code: {
    required: true,
  },
  monthly: {
    required: true,
    natural_min: 0,
    natural_max: 1000,
  },
};
</script>

<template>
  <section>
    <Alert v-if="existsInCache" class="my-4" type="alert-warning">
      Querido Usuario. No puede continuar porque hay una matrícula o inscripción en proceso. Para finalizar o cancelar
      dicha matricula
      <RouterLink :to="{ name: 'invoice' }" class="fw-medium text-secondary"> Click aquí </RouterLink>
    </Alert>
    <template v-else>
      <Form :validation-schema="schema" class="space-b-1" @submit="handleSubmitClick">
        <h6
          class="text-title pointer fst-italic user-select-none"
          data-bs-toggle="collapse"
          data-bs-target="#advance"
          aria-expanded="true"
        >
          {{ mode + " Matrícula " + fullyear }}
        </h6>
        <div id="advance" class="collapse" :class="{ show: mode === 'Nueva' }">
          <div class="myround p-3 border border-1 border-warning space-b-1">
            <MSelect
              v-model="register.section_code"
              name="section_code"
              label="Grado/grupo de la matrícula"
              :options="sections"
              opt-label="full_name"
              @update:model-value="handleSectionChange"
            />
            <MSelect v-model="register.priority" label="Turno" name="priority">
              <option :value="0" selected>Turno múltiple</option>
              <option v-for="item in priorities" :key="item.order" :value="item.order">
                {{ prdecorated[item.order] }}
              </option>
            </MSelect>
            <MInput v-model="register.monthly" name="monthly" type="number" label="Pago de inscripción/mensualidad" />
          </div>
        </div>
        <div class="bg-body p-3 myround space-b-1">
          <MSwitch id="consV" v-model="consV" text="Generar Constancia de Vacancia (Nuevo estudiante)." />
          <MSwitch id="state" v-model="state" text="Registrar estado como Activo" />
          <MSwitch
            id="invoice"
            v-model="invoicing"
            :dis="mode === 'Modificar'"
            text="Realizar también el pago de matricula"
          />
        </div>
        <div class="text-center">
          <MSubmit :is-loading="isLoading" color="btn-warning">
            {{ invoicing ? "Ir a Ingresos" : "Registrar" }}
          </MSubmit>
        </div>
      </Form>
    </template>
    <template v-if="invoicing">
      <IncomeDetail @ok="storeData" />
    </template>
  </section>
</template>

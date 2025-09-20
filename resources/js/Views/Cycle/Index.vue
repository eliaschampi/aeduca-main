<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import Empty from "../../Components/Ui/Empty.vue";
import cache from "../../core/cache";
import {
  delScApi,
  fetchAllDgApi,
  fetchCyclesApi,
  fetchSumaryApi,
  fetchTeachersApi,
  finalizeCyApi,
  setScApi,
  updateScApi,
} from "../../http";
import { useCycleStore } from "../../pinia/cycle";
import { useSnackbarStore } from "../../pinia/snackbar";
import CycleItem from "./CycleItem.vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import { useModalStore } from "../../pinia/modal";
import { useSectionStore } from "../../pinia/section";
import { Form, defineRule } from "vee-validate";
import { useDegreeStore } from "../../pinia/degree";
import { useUserStore } from "../../pinia/user";
const { fetchCycles, setCycles, onSuccessFinalize } = useCycleStore();
const { successSnack } = useSnackbarStore();
const { showModal, hideModal } = useModalStore();
const cycleStore = useCycleStore();
const cycles = computed(() => {
  if (cycleStore.show_overdues && overdue.value) {
    return [overdue.value];
  }
  return cycleStore.actives;
});
const { setSectionCode } = useSectionStore();
const { increaseSectionCount } = useDegreeStore();
const { fullyear } = useUserStore();
const router = useRouter();
const degrees = ref([]);
const teachers = ref([]);
const cycleTypes = ref([]);
const selected = ref(null);
const section = ref({});
const isOk = ref(false);
const overdue = ref(null);
const counts = ref(null);
defineRule("section", (value) => {
  if (!value || !/^[A-F]{1}$/.test(value)) {
    return "Ingrese una sección válida";
  }
  return true;
});

const schema = {
  degree_code: {
    required: true,
  },
  name: {
    section: true,
  },
  tutor: {
    required: false,
  },
};

const sectionIsUpdating = computed(() => section.value.code !== undefined);

async function fetchTypes() {
  const { data } = await fetchCyclesApi();
  cycleTypes.value = data.values;
}

function handleEditClick(code) {
  cache.setItem("cycle_code", code);
  router.push({ name: "new_cycle" });
}

async function handleFinalize() {
  const { data } = await finalizeCyApi(selected.value);
  successSnack(data.message);
  hideModal("finish");
  onSuccessFinalize(selected.value);
  selected.value = null;
}

function handleRedirectClick(item, where) {
  setSectionCode(item.code);
  router.push({
    name: where,
    params: {
      degree_code: item.degree_code,
    },
  });
}

function showfinalize(code) {
  selected.value = code;
  showModal("finish");
}

async function fetchDegrees(cycle_code) {
  const { data } = await fetchAllDgApi(cycle_code);
  degrees.value = data.values;
}

async function fetchTeachers(cycle_code) {
  const spe = cycle_code.substring(4, 6);
  const { data } = await fetchTeachersApi(spe, true);
  teachers.value = data.values;
}

async function showInfo(item) {
  showModal("secInfo");
  section.value = item;
  fetchDegrees(item.cycle_code);
  fetchTeachers(item.cycle_code);
}

async function fetchSumary(value) {
  const { data } = await fetchSumaryApi(value);
  return data.values;
}

async function handleSecSubmit() {
  const store = async () => {
    if (!sectionIsUpdating.value) {
      const { data } = await setScApi({
        ...section.value,
        code: `${section.value.degree_code}${section.value.name}`,
      });
      increaseSectionCount();
      return data.message;
    }
    const { data } = await updateScApi(section.value, section.value.code);
    return data.message;
  };
  const message = await store();
  section.value.updater(true);
  successSnack(message);
  hideModal("secInfo");
}

async function handleDestroySection() {
  const { data } = await delScApi(section.value.code);
  section.value.updater(true);
  successSnack(data.message);
  hideModal("secInfo");
}

async function fetchCounts() {
  const cached = cache.getItem("counts");
  if (cached) {
    counts.value = cached.r_count;
  }
}

onMounted(() => {
  fetchTypes();
  setCycles();
  fetchCycles();
  fetchCounts();
});
</script>

<template>
  <section id="ciclo">
    <MRouter v-can="'A'" class="fixed-action-btn" :to="{ name: 'new_cycle' }"> Aperturar </MRouter>
    <Panel :title="`Resumen anual ${fullyear}`">
      <div class="card-subtitle mb-3">Registro de ciclos académicos aperturados</div>
      <div class="space my-3">
        <span class="fw-medium"> Estudiantes matriculados: </span>
        <span> {{ counts || "Obteniendo información..." }} </span>
      </div>
      <MSwitch
        v-model="cycleStore.show_overdues"
        :text="`${!cycleStore.show_overdues ? 'Mostrar' : 'Ocultar'} ciclos anteriores`"
        class="mb-4"
      />
      <template v-if="cycleStore.show_overdues">
        <MSelect
          v-model="overdue"
          standalone
          name="expired"
          style="max-width: 15rem"
          :options="cycleStore.overdues"
          placeholder="Ciclos anteriores"
          opt-value="all"
          :opt-label="(item) => `${item.full_name} (${item.title})`"
        />
      </template>
    </Panel>
    <div class="mygrid g18 mt-4">
      <template v-for="item in cycles" :key="item.code">
        <CycleItem
          :cycle="item"
          :cycle-types="cycleTypes"
          :fetch-sumaries="fetchSumary"
          @edit="handleEditClick"
          @show-finalize="showfinalize"
          @redirect="handleRedirectClick"
          @show-info="showInfo"
        />
      </template>
    </div>
    <Empty v-show="cycleStore.actives.length === 0" title="Aun no se ha aperturado ningun ciclo" />
    <Dialog id="finish" title="Finalizar Ciclo" btn-name="Enviar" :disabled="!isOk" @ok="handleFinalize">
      <Alert>
        Al finalizar el ciclo, todos los estudiantes matriculados se actualizarán a
        <b>Finalizado</b>.
      </Alert>
      <MCheck id="isOkID" v-model="isOk" text="Confirmo que esta acción es permanente y no se podrá revertir" />
    </Dialog>
    <Dialog id="secInfo" :title="`${sectionIsUpdating ? 'Actualizar' : 'Crear'} sección`">
      <Form v-slot="{ meta: { valid } }" :validation-schema="schema" @submit="handleSecSubmit">
        <div class="space-b">
          <MSelect
            v-model="section.degree_code"
            name="degree_code"
            label="Grado"
            opt-label="full_name"
            :options="degrees"
            :disabled="sectionIsUpdating"
          />
          <MInput v-model="section.name" name="name" label="Sección" :disabled="sectionIsUpdating" />
          <small>El nombre de la Sección proporcionado no deberá existir en el Grado Actual</small>
          <hr />
          <MSelect
            v-model="section.tutor"
            name="tutor"
            label="Tutor responsable"
            :opt-label="(item) => `${item.person.name} ${item.person.lastname}`"
            :options="teachers"
            opt-value="dni"
          />
          <div class="space">
            <span class="fw-medium">Estudiantes matriculados:</span>
            <div class="badge bg-secondary-subtle text-secondary">
              {{ section.rcount || 0 }}
            </div>
          </div>
          <div class="text-center">
            <MSubmit icon="ion-ios-send" color="btn-success" :disabled="!valid"> Guardar </MSubmit>
          </div>
        </div>
      </Form>
      <template #foot>
        <div
          v-show="sectionIsUpdating && section.rcount === 0"
          class="text-center pointer fst-italic bg-light p-3 rounded"
          @click="handleDestroySection"
        >
          <i class="text-danger icon ion-md-information-circle"></i> Eliminar sección permanentemente
        </div>
      </template>
    </Dialog>
  </section>
</template>

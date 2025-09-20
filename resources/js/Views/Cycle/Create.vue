<script setup>
import { computed, onMounted, onUnmounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import cache from "../../core/cache";
import { fetchByCodeCyApi, fetchCountByTypeCyApi, fetchCyclesApi, setCyApi, updateCyApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
import FormTab from "../../Components/Card/FormTab.vue";
const { fullyear } = useUserStore();
const { successSnack } = useSnackbarStore();
const router = useRouter();
const cycle = reactive({
  type: "",
  monthly: 0.0,
  title: "Regular",
  code: "",
  from: "",
  to: "",
  attendance: [],
});

const code = ref("");
const isLoading = ref(false);
const ctypes = ref([]);
const attendance = ref([{ order: 1, entry_time: "07:00", tolerance: 5 }]);

const mapped = computed(() => {
  return Object.keys(ctypes.value).map((key) => {
    return { code: key, name: ctypes.value[key] };
  });
});

async function fetchTypes() {
  const { data } = await fetchCyclesApi();
  ctypes.value = data.values;
}

function toggleAttendancePrioority() {
  if (attendance.value.length > 1) {
    attendance.value.pop();
  } else {
    attendance.value.push({
      order: attendance.value.length + 1,
      entry_time: "16:00",
      tolerance: 5,
    });
  }
}

function storeData() {
  cycle.attendance = attendance.value;
  if (!code.value) {
    return setCyApi(cycle);
  }
  return updateCyApi(cycle);
}

async function handleSubmitClick() {
  isLoading.value = true;
  const { data } = await storeData();
  router.push({ name: "main_cycle" });
  successSnack(data.message);
}

async function handleChangeType(type) {
  const { data } = await fetchCountByTypeCyApi(type);
  cycle.code = data.value;
}

async function fetchByCode() {
  const { data } = await fetchByCodeCyApi(code.value);
  Object.assign(cycle, data.value);
  attendance.value = data.value.attendance;
}

onMounted(() => {
  fetchTypes();
  code.value = cache.getItem("cycle_code");
  if (code.value) {
    fetchByCode();
  }
});

onUnmounted(() => {
  cache.removeItem("cycle_code");
});

const schema = computed(() => ({
  type: {
    required: true,
  },
  code: {
    required: true,
  },
  title: {
    required: true,
    max: 13,
  },
  monthly: {
    required: true,
    natural_min: 0,
    natural_max: 1000,
  },
  modular_code: {
    max: 20,
    min: 5,
  },
  from: {
    required: true,
    before: cycle.to,
    after: `${fullyear}-01-01`,
  },
  to: {
    required: true,
    before: `${fullyear + 1}-01-01`,
    after: cycle.from,
  },
  d_a: {
    required: !code.value,
    natural_max: cycle.d_b,
  },
  d_b: {
    required: !code.value,
    natural_min: cycle.d_a,
  },
}));
</script>

<template>
  <FormTab
    id="newCycle"
    :btn-name="!code ? 'Aperturar' : 'Modificar'"
    :is-loading="isLoading"
    :schema="schema"
    @ok="handleSubmitClick"
  >
    <template #tabs>
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#main" role="tab">
          <i class="icon ion-md-list d-md-none"></i>
          <span class="d-none d-md-block d-lg-block"> Datos generales </span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#config" role="tab">
          <i class="icon ion-md-settings d-md-none"></i>
          <span class="d-none d-md-block d-lg-block"> Configuración </span>
        </a>
      </li>
    </template>

    <div id="main" class="tab-pane fade show active" role="tabpanel">
      <div class="d-none">
        <MInput v-model="cycle.code" name="code" label="code" />
      </div>
      <div class="row mt-4 gx-3">
        <MSelect
          v-model="cycle.type"
          name="type"
          class="col-md-6 mb-3"
          label="Modalidad"
          :options="mapped"
          :disabled="!!code"
          @update:model-value="handleChangeType"
        />
        <MSelect
          v-model="cycle.title"
          name="title"
          class="col-md-6 mb-3"
          label="Ciclo"
          :options="['Regular', 'Verano', 'Intensivo', 'Vacacional', 'Reforzamiento', 'Otro']"
        />
      </div>
      <div class="row align-items-center gx-3">
        <MInput
          v-model="cycle.monthly"
          name="monthly"
          label="Mensualidad/Inscripción"
          class="col-md-6 mb-3"
          type="number"
        />
        <MInput v-model="cycle.modular_code" name="modular_code" label="Código modular" class="col-md-6 mb-3" />
      </div>
      <template v-if="!code">
        <div class="fw-medium">Grados</div>
        <div class="row gx-3">
          <MSelect
            v-model="cycle.d_a"
            name="d_a"
            label="Desde"
            :options="6"
            class="col-md-6 mb-3"
            :opt-label="(d) => [null, '1ro', '2do', '3ro', '4to', '5to', '6to'][d]"
          />
          <MSelect
            v-model="cycle.d_b"
            name="d_b"
            label="Hasta"
            :options="6"
            class="col-md-6 mb-3"
            :opt-label="(d) => [null, '1ro', '2do', '3ro', '4to', '5to', '6to'][d]"
          />
        </div>
      </template>
    </div>
    <div id="config" class="tab-pane fade" role="tabpanel">
      <div class="fw-medium my-4">Duración</div>
      <div class="row gx-3">
        <MInput v-model="cycle.from" name="from" label="Inicio" type="date" class="col-md-6 mb-3" />
        <MInput v-model="cycle.to" name="to" label="Fin" type="date" class="col-md-6 mb-3" />
      </div>
      <div class="fw-medium mb-3">Horarios de ingreso</div>
      <template v-for="item in attendance" :key="item.order">
        <div class="row gx-3">
          <MInput
            v-model="item.entry_time"
            type="time"
            :name="`entry_time${item.order}`"
            class="col-md-3 mb-3"
            label="Hora de Ingreso"
          />
          <MInput
            v-model="item.tolerance"
            type="number"
            :name="`tolerance${item.order}`"
            class="col-md-3 mb-3"
            label="Tolerancia"
          />
        </div>
      </template>
      <div class="d-flex space pointer" @click="toggleAttendancePrioority">
        <b class="icon ion-md-add"></b>
        <span>{{ attendance.length > 1 ? "Desabilitar" : "Habilitar" }} turno tarde</span>
      </div>
    </div>
  </FormTab>
</template>

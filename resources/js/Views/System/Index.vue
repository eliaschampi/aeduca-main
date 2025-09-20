<script setup>
import { computed, inject, onMounted, onUnmounted, reactive, ref } from "vue";
import { fetchActivesApi, fetchConfigApi, updateConfigApi } from "../../http";
import { useRefreshStore } from "../../pinia/refresh";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
import Company from "./Company.vue";
import UploadTemplate from "./components/UploadTemplate.vue";
const { successSnack } = useSnackbarStore();
const { branch_code } = useUserStore();
const emitter = inject("emitter");
const config = reactive({
  entry_time: "07:00",
  tolerance: 0,
  st_att_left: [],
  abs_enabled: false,
  abs1: [],
  abs2: [],
  pgnt: 0,
});
const cycles = ref([]);
const abs = ref("abs1");
const uploadTemplate = ref(null);
const cycle_code = ref("");
const filteredCycle = computed(() => {
  return cycles.value.filter((cycle) => {
    return cycle.branch_code === branch_code;
  });
});
async function fetchData() {
  const { data } = await fetchConfigApi();
  Object.assign(config, data.values);
}

async function fetchCycles() {
  const { data } = await fetchActivesApi();
  cycles.value = data.values;
}

async function handleSubmit() {
  const { limited } = useUserStore();
  if (limited) return;
  const { data } = await updateConfigApi(config, "core");
  successSnack(data.message);
}

function handleAddCycleItem() {
  if (!cycle_code.value) {
    return;
  }

  if (config[abs.value].indexOf(cycle_code.value) !== -1) {
    successSnack("La opción seleccionada ya ha sido agregado");
    return;
  }

  config[abs.value].push(cycle_code.value);
}

function handleRemoveCycle(item) {
  config[abs.value] = config[abs.value].filter((cycle) => cycle !== item);
}

function decorator(item) {
  const result = cycles.value.find((cycle) => cycle.code === item);
  if (!result) {
    return "Ciclo o modalidad vencido";
  }
  let branch = "";
  if (result.branch_code !== branch_code) {
    branch = "(Otra sede)";
  }
  return `${result.full_name} ${result.title} ${branch}`;
}

function reAuthenticate() {
  const { showRefresh } = useRefreshStore();
  showRefresh("Se requiere autenticación", "/check");
}

onMounted(async () => {
  await fetchCycles();
  fetchData();
  emitter.on("reauthed", handleSubmit);
});

onUnmounted(() => {
  emitter.off("reauthed", handleSubmit);
});
</script>

<template>
  <div class="row">
    <div class="col-lg-5 space-b-1">
      <Panel title="Logo">
        <div class="text-center">
          <img src="/default/logo.png" alt="Logo" class="mb-3 mx-auto" width="150" />
          <p class="opacity-50">
            <small>Logo de la institución educativa</small>
          </p>
        </div>
      </Panel>
      <Panel title="Formatos">
        <div class="space-b">
          <div class="space d-flex align-items-center fw-medium opacity-75">
            <i class="icon pointer ion-md-folder icon-md"></i>
            <span>Formato de carnet (card)</span>
          </div>
          <span class="d-block">
            El carnet debe tener un formato png, con dimensiones 1,280 × 824 píxeles y menos de 200kb de peso. Además,
            el diseño del carnet debe estar sujeto a los reglamentos vigentes.
          </span>
          <div class="space">
            <MButton
              size="btn-sm"
              color="btn-inverse-success"
              icon="ion-md-cloud-cloud-upload"
              @ok="uploadTemplate.handleShowModal('card')"
            >
              Subir
            </MButton>
            <a href="/default/card.png" download class="btn btn-sm btn-inverse-info"> Descargar </a>
          </div>
          <div class="space d-flex align-items-center fw-medium opacity-75">
            <i class="icon pointer ion-md-folder icon-md"></i>
            <span>Membrete de reportes (pdf)</span>
          </div>
          <span class="d-block">
            El membrete debe tener un formato png, con dimensiones A4 (210 x 297 cm) y menos de 200kb de peso.
          </span>
          <div class="space">
            <MButton
              size="btn-sm"
              color="btn-inverse-success"
              icon="ion-md-cloud-cloud-upload"
              @ok="uploadTemplate.handleShowModal('pdf')"
            >
              Subir
            </MButton>
            <a href="/default/pdf.png" download class="btn btn-sm btn-inverse-info"> Descargar </a>
          </div>
        </div>
      </Panel>
      <Panel title="Configuración del sistema" f>
        <div class="space-b-1">
          <div class="fw-medium opacity-75">Paginación: {{ config.pgnt }} registros por página</div>
          <input v-model="config.pgnt" name="pgnt" type="range" class="w-100 form-range" min="10" max="100" />
          <div class="fw-medium opacity-75">Parámetros de configuración de asistencia de los docentes</div>
          <MInput v-model="config.entry_time" name="entry_time" type="time" label="Hora de ingreso" />
          <div class="fw-medium opacity-75">Tolerancia: {{ config.tolerance }} minutos</div>
          <input v-model="config.tolerance" name="tolerance" type="range" class="w-100 form-range" min="1" max="60" />
          <div>
            <div class="fw-medium opacity-75">Asistencia por la plataforma</div>
            <div class="space-b mt-2">
              <div class="form-check">
                <input id="mondayId" v-model="config.st_att_left" class="form-check-input" type="checkbox" value="1" />
                <label class="form-check-label" for="mondayId"> Lunes </label>
              </div>
              <div class="form-check">
                <input id="tuesdayId" v-model="config.st_att_left" class="form-check-input" type="checkbox" value="2" />
                <label class="form-check-label" for="tuesdayId"> Martes </label>
              </div>
              <div class="form-check">
                <input id="wedId" v-model="config.st_att_left" class="form-check-input" type="checkbox" value="3" />
                <label class="form-check-label" for="wedId"> Miercoles </label>
              </div>
              <div class="form-check">
                <input
                  id="thursdayId"
                  v-model="config.st_att_left"
                  class="form-check-input"
                  type="checkbox"
                  value="4"
                />
                <label class="form-check-label" for="thursdayId"> Jueves </label>
              </div>
              <div class="form-check">
                <input id="fridayId" v-model="config.st_att_left" class="form-check-input" type="checkbox" value="5" />
                <label class="form-check-label" for="fridayId"> Viernes </label>
              </div>
              <div class="form-check">
                <input
                  id="saturdayId"
                  v-model="config.st_att_left"
                  class="form-check-input"
                  type="checkbox"
                  value="6"
                />
                <label class="form-check-label" for="saturdayId"> Sábado </label>
              </div>
            </div>
          </div>
          <div class="fw-medium opacity-75">Registro de inasistencias</div>
          <MSwitch id="abs_enabled" v-model="config.abs_enabled" text="Habilitar registro automático" />
          <blockquote class="blockquote">
            <p>
              Aquí, agrega todos los ciclos académicos o modalidades activas al registro automático de inasistencias
              para cada turno.
              <i>
                Recuerda, la inasistencia para el turno de la mañana se registrará automáticamente a las 9:00 a.m., y la
                inasistencia para el turno de la tarde se registrará a las 6:00 p.m
              </i>
            </p>
          </blockquote>
          <div class="d-flex space">
            <div class="form-check">
              <input id="abs1Id" v-model="abs" class="form-check-input" type="radio" value="abs1" name="turn" />
              <label class="form-check-label" for="abs1Id"> Turno Mañana </label>
            </div>
            <div class="form-check">
              <input id="abs2Id" v-model="abs" class="form-check-input" type="radio" value="abs2" name="turn" />
              <label class="form-check-label" for="abs2Id"> Turno Tarde </label>
            </div>
          </div>
          <div class="d-flex align-items-center space">
            <MSelect
              v-model="cycle_code"
              name="cycle_code_config"
              standalone
              class="w-100"
              :options="filteredCycle"
              label="Ciclo Académico"
              :opt-label="(item) => `${item.full_name} ${item.title}`"
            />
            <MAction icon="add" tool="Agregar" @action="handleAddCycleItem" />
          </div>
          <div class="border-rounded p-3" style="max-width: max-content">
            <div class="text-title title">Ciclos</div>
            <ul class="list-group list-group-flush">
              <template v-for="item in config[abs]" :key="item">
                <li class="list-group-item d-flex align-items-center space">
                  <em>{{ decorator(item) }}</em>
                  <MAction
                    v-show="item.substring(7, 8) === `${branch_code}`"
                    icon="remove-circle"
                    tool="Quitar"
                    color="danger"
                    @action="handleRemoveCycle(item)"
                  />
                </li>
              </template>
              <div v-show="!config[abs].length" class="text-center opacity-50">
                Agregue aquí los ciclos o modalidades
              </div>
            </ul>
          </div>
        </div>
        <template #foot>
          <Footext>
            <MButton @ok="reAuthenticate">Guardar Cambios</MButton>
          </Footext>
        </template>
      </Panel>
      <Company />
      <UploadTemplate ref="uploadTemplate" />
    </div>
  </div>
</template>

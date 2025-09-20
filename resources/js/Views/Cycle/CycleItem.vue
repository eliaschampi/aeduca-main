<script setup>
import { computed, ref } from "vue";
import { useRangeDetail } from "../../composables/range";
const emit = defineEmits(["edit", "showFinalize", "showInfo", "redirect"]);
const props = defineProps({
  cycle: {
    type: Object,
    default: () => ({}),
  },
  fetchSumaries: Function,
  cycleTypes: Object,
});
const selected = ref({});
const sumaries = ref([]);
const degrees = computed(() => {
  return [...new Set(sumaries.value.map((item) => item.degree_code))];
});

const filtered = computed(() => {
  return sumaries.value.filter((item) => item.degree_code === selected.value);
});

const total = computed(() => {
  return sumaries.value.reduce((acu, item) => parseInt(item.rcount) + acu, 0);
});

async function fetchData(force = false) {
  if (sumaries.value.length === 0 || force) {
    sumaries.value = await props.fetchSumaries(props.cycle.code);
    if (sumaries.value.length) {
      selected.value = sumaries.value[0].degree_code;
    }
  }
}

function emitShowInfo(payload) {
  emit("showInfo", Object.assign(payload, { updater: fetchData, cycle_code: props.cycle.code }));
}

const detailDays = computed(() => {
  const { total, passed, percentage } = useRangeDetail(props.cycle.from, props.cycle.to);
  return {
    total,
    passed,
    percentage,
  };
});
</script>

<template>
  <Card :title="props.cycleTypes[props.cycle.type]" :f="false">
    <template #rb>
      <div v-can="'AS'" class="dropstart">
        <span :id="`dropdown${cycle.code}`" class="pointer p-2" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="icon ion-md-more icon-md opacity-75"></i>
        </span>
        <div class="dropdown-menu">
          <div class="dropdown-item" @click="emit('edit', props.cycle.code)">
            <i class="icon ion-md-create text-success"></i>
            Modificar
          </div>
          <div class="dropdown-item" @click="emit('showFinalize', props.cycle.code)">
            <i class="icon ion-md-flag text-info"></i>
            Finalizar
          </div>
          <div v-show="detailDays.percentage !== '100.00'" class="dropdown-item" @click="emitShowInfo({})">
            <i class="icon ion-md-add text-secondary"></i>
            Crear Sección
          </div>
        </div>
      </div>
    </template>
    <div class="space-b">
      <div class="card-subtitle">
        <span>Desde {{ `${$date(cycle.from)} hasta ${$date(cycle.to)}.` }}</span>
        <small class="fw-medium opacity-50">
          Han transcurrido {{ detailDays.passed }} de {{ detailDays.total }} dias
        </small>
      </div>
      <div class="d-flex align-items-center space">
        <b>Ciclo:</b>
        <span class="badge bg-secondary-subtle text-secondary-emphasis">
          {{ props.cycle.title }}
        </span>
      </div>
      <div class="progress" style="height: 25px">
        <div
          class="progress-bar progress-bar-striped progress-bar-animate bg-success"
          role="progressbar"
          aria-valuemax="100"
          aria-valuemin="0"
          :aria-valuenow="detailDays.percentage"
          :style="{ width: detailDays.percentage + '%', height: '25px' }"
        >
          <b>{{ detailDays.percentage + "%" }}</b>
        </div>
      </div>
      <ul class="nav nav-line nav-fill" role="tablist" style="flex-wrap: nowrap">
        <li class="nav-item">
          <a class="nav-link active" data-bs-toggle="tab" :href="`#sh${cycle.code}`" role="tab">
            <i class="icon ion-md-partly-sunny"></i>
          </a>
        </li>
        <li class="nav-item" @click="fetchData()">
          <a class="nav-link" data-bs-toggle="tab" :href="`#rp${cycle.code}`" role="tab">
            <i class="icon ion-md-people"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="tab" :href="`#info${cycle.code}`" role="tab">
            <i class="icon ion-md-list"></i>
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div :id="`sh${cycle.code}`" class="tab-pane fade in show active" role="tabpanel">
          <div class="table-responsive">
            <div>Horarios de ingreso</div>
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Turno</th>
                  <th>Ingreso</th>
                  <th>Tolerancia</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="att in props.cycle.attendance" :key="att.order">
                  <td>{{ ["Mañana", "Tarde"][att.order - 1] }}</td>
                  <td>{{ att.entry_time }}</td>
                  <td>{{ att.tolerance }} mins</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div :id="`rp${cycle.code}`" class="tab-pane fade" role="tabpanel">
          <div class="d-flex space pb-3 overflow-auto">
            <template v-for="item in degrees" :key="item">
              <div
                class="badge bg-success-subtle text-success-emphasis pointer"
                :class="[
                  {
                    'border border-success border-2': item === selected,
                  },
                ]"
                @click="selected = item"
              >
                {{ [null, "1ro", "2do", "3ro", "4to", "5to", "6to"][item.substring(8, 9)] }}
              </div>
            </template>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-striped">
              <thead>
                <tr>
                  <th>Sección</th>
                  <th>Estudiantes</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in filtered" :key="item.code">
                  <td class="text-center">
                    {{ item.name }}
                  </td>
                  <td>
                    <div class="d-flex align-items-center space">
                      <MAction icon="people" tool="Estudiantes" @action="emit('redirect', item, 'section_student')" />
                      <b>
                        {{ item.rcount }}
                      </b>
                    </div>
                  </td>
                  <td>
                    <div class="btn-group">
                      <span
                        :id="`dropdown${item.code}`"
                        class="pointer p-2"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                      >
                        <i class="icon ion-md-more icon-md text-info"></i>
                      </span>
                      <div class="dropdown-menu">
                        <div v-can="'AS'" class="dropdown-item pointer" @click="emitShowInfo(item)">Ver detalles</div>
                        <div class="dropdown-item pointer" @click="emit('redirect', item, 'schedule')">Horario</div>
                        <div class="dropdown-item pointer" @click="emit('redirect', item, 'main_family')">
                          Apoderados
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="!filtered.length" class="text-center">
              <i class="opacity-50">Aun no hay secciones o grupos creados</i>
            </div>
            <div v-else class="d-flex align-items-center space">
              <span>Total de estudiantes: </span>
              <b class="text-warning">{{ total }}</b>
            </div>
          </div>
        </div>
        <div :id="`info${cycle.code}`" class="tab-pane fade space-b-1 opacity-75" role="tabpanel">
          <MPlain wraped label="Mensualidad/Inscripción:" :value="$currency(props.cycle.monthly)" />
          <MPlain wraped label="Código Modular:" :value="props.cycle.modular_code || 'No establecido'" />
        </div>
      </div>
    </div>
  </Card>
</template>

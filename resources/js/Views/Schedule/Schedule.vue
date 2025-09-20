<script setup>
import { computed, reactive, ref } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import MySection from "../../Components/Views/MySection.vue";
import days from "../../data/weekDays.json";
import {
  delShApi,
  destroyOpApi,
  fetchOpsApi,
  fetchMainShApi,
  updateShApi,
  fetchTeachersApi,
  fetchAllCourses,
  updateOpApi,
  storeOpApi,
  setShApi,
} from "../../http";
import { useModalStore } from "../../pinia/modal";
import { useSectionStore } from "../../pinia/section";
import { useSnackbarStore } from "../../pinia/snackbar";
import AddItem from "./AddItem.vue";
import PersonCard from "../../Components/Views/PersonCard.vue";
import { Form } from "vee-validate";
const { successSnack } = useSnackbarStore();
const { showModal, hideModal } = useModalStore();
const day = ref(1);
const isOk = ref(false);
const schedules = ref([]);

const originalOp = {
  teacher_dni: "",
  course_code: "",
  sts: [],
};
const createSh = ref(null);
const newOp = reactive({ ...originalOp });
const schema = {
  teacher_dni: {
    required: true,
  },
  course_code: {
    required: true,
  },
};
const teachers = ref([]);
const courses = ref([]);
const ops = ref([]);
const isLoading = ref(false);

const filtered = computed(() => {
  return schedules.value.filter((item) => item.day === day.value || item.day === 0);
});

const fdays = computed(() => {
  return Object.values(days).slice(1, 7);
});

const decorator = (section_code) => {
  if (!section_code) return null;
  return `${section_code.substring(8, 10)} de ${section_code.substring(4, 6)}`;
};

function showDeleteOp(item) {
  Object.assign(newOp, item);
  showModal("delOP");
}

async function fetchSchedules(code) {
  const { data } = await fetchMainShApi(code);
  schedules.value = data.values;
}

async function fetchOps(code) {
  const {
    data: { values },
  } = await fetchOpsApi(code);
  ops.value = values;
}

async function fetchData() {
  const { code } = useSectionStore();
  isLoading.value = true;
  fetchOps(code);
  fetchSchedules(code);
  isLoading.value = false;
}

async function fetchTeachers() {
  const { code } = useSectionStore();
  const spe = code.substring(4, 6);
  const { data } = await fetchTeachersApi(spe, true);
  teachers.value = data.values;
}

async function fetchCourses() {
  const { data } = await fetchAllCourses();
  courses.value = data.values;
}

function showCreateModal(payload = originalOp) {
  showModal("createOpModal");
  if (!payload.code) {
    const { code } = useSectionStore();
    payload.sts = [code];
    delete newOp.code;
  }
  Object.assign(newOp, payload);
  fetchTeachers();
  fetchCourses();
}

async function handleDeleteOP() {
  if (!newOp.code) return;
  const { data } = await destroyOpApi(newOp.code);
  successSnack(data.message);
  Object.assign(newOp, {});
  fetchData();
  hideModal("showDeleteOp");
}

async function handleSubSched(sh) {
  const store = () => {
    const isDuplicated = () => {
      return schedules.value.some(({ code, day, from_time }) => {
        return day === sh.day && from_time === sh.from_time && code !== sh.code;
      });
    };
    if (isDuplicated()) {
      return Promise.resolve({ data: { message: "Registro duplicado" } });
    }
    if (!sh.code) {
      return setShApi(sh);
    }
    return updateShApi(sh);
  };
  const { code } = useSectionStore();
  const { data } = await store();
  fetchSchedules(code);
  successSnack(data.message);
  hideModal("newSchedule");
}

async function handleOpSubmit() {
  const store = (op) => {
    const isDuplicated = () => {
      return ops.value.some(({ code, teacher_dni, course_code }) => {
        return teacher_dni === op.teacher_dni && course_code === op.course_code && code !== op.code;
      });
    };
    if (isDuplicated()) {
      return Promise.resolve({ data: { message: "Registro duplicado" } });
    }
    if (!op.code) {
      return storeOpApi(op);
    }
    return updateOpApi(op.code, op);
  };
  const { data } = await store(newOp);
  const { code } = useSectionStore();
  successSnack(data.message);
  hideModal("createOpModal");
  fetchOps(code);
}

async function handleDeleteSchedule(sh) {
  const { data } = await delShApi(sh.code);
  successSnack(data.message);
  schedules.value.splice(schedules.value.indexOf(sh), 1);
}
</script>

<template>
  <section class="space-b-1">
    <Panel title="Docentes y horario">
      <div class="d-flex space mb-3">
        <MySection @done="fetchData" />
        <MButton v-can="'AS'" color="btn-info btn-sm" @ok="showCreateModal()"> Agregar docente </MButton>
      </div>
    </Panel>
    <div class="mygrid g18">
      <template v-for="item in ops" :key="item.code">
        <PersonCard
          :dni="item.teacher_dni"
          :image="item.person.profile?.image"
          :name="item.person.name"
          :lastname="item.person.lastname"
          route="t_schedule"
        >
          <div class="text-small d-flex">
            <div class="d-flex align-items-center me-1">
              <span class="boli bg-success me-1"></span>
              <span class="fw-medium">Curso: </span>
            </div>
            <span>{{ item.course.name }}</span>
          </div>
          <template #right>
            <div v-can="'AS'" class="dropstart">
              <span
                :id="`dropdown${item.teacher_dni}`"
                class="pointer p-2"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="icon ion-md-more icon-md opacity-75"></i>
              </span>
              <div class="dropdown-menu">
                <div class="dropdown-item" @click="showCreateModal(item)">
                  <i class="icon ion-md-create text-success"></i>
                  Modificar
                </div>
                <div class="dropdown-item" @click="showDeleteOp(item)">
                  <i class="icon ion-md-add text-danger"></i>
                  Eliminar
                </div>
              </div>
            </div>
          </template>
        </PersonCard>
      </template>
    </div>
    <Card title="Horario Semanal" :f="false">
      <template #rb>
        <MButton
          :disabled="!ops.length"
          color="btn-inverse-info"
          class="btn-icon"
          icon="icon ion-md-add"
          @ok="createSh.show({ day })"
        />
      </template>
      <ul class="nav nav-pills mb-4">
        <li v-for="d in fdays" :key="d.code" class="nav-item pointer" @click="day = d.code">
          <div class="nav-link" :class="{ active: day === d.code }">
            {{ d.name }}
          </div>
        </li>
      </ul>
      <MTable :columns="['Curso', 'Desde', 'Hasta', 'Acciones']" :data="filtered" :head="false">
        <template #data="{ rows }">
          <tr v-for="item in rows" :key="item.code">
            <td>{{ item.op.course.name }}</td>
            <td>{{ item.from_time }}</td>
            <td>{{ item.to_time }}</td>
            <td v-can="'AS'" class="space">
              <MAction @action="createSh.show(item)" />
              <MAction icon="trash" color="danger" tool="Eliminar" @action="handleDeleteSchedule(item)" />
            </td>
          </tr>
        </template>
      </MTable>
    </Card>
    <AddItem ref="createSh" :days="days" :ops="ops" @ok="handleSubSched" />
    <Dialog id="delOP" title="Eliminar docente">
      <Alert :dismisable="false">
        Se eliminará el horario y los cursos del docente en esta sección.
        <i class="text-info-emphasis"> tambien se borrará el contenido de su plataforma. </i>
      </Alert>
      <MCheck id="isOkID" v-model="isOk" text="Confirmo que esta acción es permanente y no se podrá revertir" />
      <template #foot>
        <div class="text-center p-3">
          <MButton color="btn-inverse-danger" :disabled="!isOk" @ok="handleDeleteOP"> Confirmar </MButton>
        </div>
      </template>
    </Dialog>
    <Dialog id="createOpModal" :title="`${!newOp.code ? 'Agregar' : 'Modificar'} docente`">
      <Form v-slot="{ meta: { valid } }" class="space-b-1" :validation-schema="schema" @submit="handleOpSubmit">
        <MSelect
          v-model="newOp.teacher_dni"
          name="teacher_dni"
          label="Docente"
          :opt-label="(item) => item.person.name"
          :options="teachers"
          opt-value="dni"
        />
        <MSelect v-model="newOp.course_code" name="course_code" label="Curso" :options="courses" />
        <div class="bg-body p-2 myround">
          <ul class="list-group list-group-flush">
            <li class="list-group-item"><b>Secciones</b></li>
            <li class="list-group-item align-items-center">
              <i class="icon ion-md-arrow-round-forward text-success"></i>
              {{ decorator(newOp.sts[0]) }}
            </li>
            <div class="list-group-item pointer opacity-50 text-primary text-small">Agregar otra sección</div>
          </ul>
        </div>
        <div class="text-center">
          <MSubmit icon="ion-ios-send" color="btn-success" :disabled="!valid"> Guardar </MSubmit>
        </div>
      </Form>
      <template #foot> &nbsp; </template>
    </Dialog>
  </section>
</template>

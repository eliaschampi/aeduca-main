<script setup>
import { useForm } from "vee-validate";
import { reactive, ref } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import { delCourse, fetchAllCourses, setCourse, updateCourse } from "../../http";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
const { successSnack, showSnack } = useSnackbarStore();
const courses = ref([]);
const { showModal, hideModal } = useModalStore();
const columns = ["Código", "Curso", "Acciones"];

const course = reactive({
    name: "",
});

const schema = {
    name: {
        required: true,
        min: 2,
        max: 50,
    },
};

const { handleSubmit, resetForm } = useForm({
    validationSchema: schema,
});

async function fetchData() {
    const { data } = await fetchAllCourses();
    courses.value = data.values;
}

async function handleShowModal(item) {
    Object.assign(course, item);
    showModal("courseModal");
}

function handleDeleteCourse(item) {
    showSnack({
        text: "¿Está seguro de eliminar el curso?",
        button: "Confirmar",
        action: async () => {
            const { data } = await delCourse(item.code);
            courses.value.splice(courses.value.indexOf(item), 1);
            successSnack(data.message);
        },
    });
}

function store() {
    if (course.code) {
        return updateCourse(course);
    }
    return setCourse(course);
}

const onSubmit = handleSubmit(async () => {
    const { data } = await store();
    successSnack(data.message);
    hideModal("courseModal");
    fetchData();
});

function handleCancel() {
    resetForm({ errors: {} });
    course.code = undefined;
}
</script>
<template>
    <section id="course">
        <card title="Registro de Cursos">
            <template #rb>
                <m-button
                    data-bs-toggle="modal"
                    data-bs-target="#courseModal"
                    color="btn-inverse-info"
                    class="btn-icon"
                    icon="icon ion-md-add"
                />
            </template>
            <m-table :columns="columns" :data="courses" :fetch="fetchData" :head="false">
                <template #data="{ rows }">
                    <tr v-for="item in rows" :key="item.code">
                        <td>{{ item.code }}</td>
                        <td>{{ item.name }}</td>
                        <td class="space">
                            <m-action @action="handleShowModal(item)" />
                            <m-action
                                icon="trash"
                                color="danger"
                                tool="Eliminar"
                                @action="handleDeleteCourse(item)"
                            />
                        </td>
                    </tr>
                </template>
            </m-table>

            <template #foot>
                <footext :title="`${courses.length} cursos`" />
            </template>
        </card>
        <Dialog
            id="courseModal"
            btn-name="Guardar"
            :title="course.code ? 'Editar curso' : 'Nuevo curso'"
            @ok="onSubmit"
            @cancel="handleCancel"
        >
            <div class="row gx-2">
                <m-input
                    v-model="course.name"
                    class="col-md-12"
                    name="name"
                    label="Nombre del Curso"
                />
            </div>
        </Dialog>
    </section>
</template>

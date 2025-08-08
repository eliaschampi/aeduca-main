<script setup>
import { computed, ref, watch } from "vue";
import PersonLink from "../../Components/Views/PersonLink.vue";
import RelationModal from "../../Components/Views/RelationModal.vue";
import relation_types from "../../data/relationTypes.json";
import { fetchStudentsFmApi, removeStudentFmApi } from "../../http";
import { useFamilyStore } from "../../pinia/family";
import { useSnackbarStore } from "../../pinia/snackbar";
const storedDni = computed(() => useFamilyStore().dni);
const { showSnack, successSnack } = useSnackbarStore();
const students = ref([]);

function removeStudent(item) {
    showSnack({
        text: "¿Desea eliminar al estudiante?",
        button: "Confirmar",
        action: async () => {
            const { data } = await removeStudentFmApi(storedDni.value, item.dni);
            students.value = students.value.filter((student) => student.dni !== item.dni);
            successSnack(data.message);
        },
    });
}
async function fetchData(dni = storedDni.value) {
    const { data } = await fetchStudentsFmApi(dni);
    students.value = data.values;
}

watch(storedDni, (val) => fetchData(val), { immediate: true });
</script>
<template>
    <section class="mt-5">
        <panel
            title="Estudiantes"
            :f="true"
        >
            <m-table
                :columns="['Apoderado:', 'Encargado', 'Estudiante', 'Acciones']"
                :data="students"
                :head="false"
                emptytext="Aún no pertenece a ningún Estudiante."
            >
                <template #data="{ rows }">
                    <tr
                        v-for="item in rows"
                        :key="item.dni"
                    >
                        <td>{{ relation_types[item.pivot.relation_type].name }}</td>
                        <td>{{ item.pivot.is_main ? "Si" : "No" }}</td>
                        <td>
                            <PersonLink
                                route="student_profile"
                                :person="item.person"
                            />
                        </td>
                        <td>
                            <m-action
                                icon="remove-circle"
                                color="danger"
                                tool="Excluir"
                                @action="removeStudent(item)"
                            />
                        </td>
                    </tr>
                </template>
            </m-table>
            <template #foot>
                <footext>
                    <m-button
                        data-bs-toggle="modal"
                        data-bs-target="#addRelation"
                        color="btn-inverse-success"
                        size="btn-sm"
                        icon="ion-md-add"
                    >
                        Agregar
                    </m-button>
                </footext>
            </template>
        </panel>
        <RelationModal
            title="estudiante"
            who="student"
            who-r="new_student"
            :dni="storedDni"
            @added="fetchData()"
        />
    </section>
</template>

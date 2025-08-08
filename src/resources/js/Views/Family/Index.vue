<script setup>
import { computed, onUnmounted, ref } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import MySection from "../../Components/Views/MySection.vue";
import PersonLink from "../../Components/Views/PersonLink.vue";
import relation_types from "../../data/relationTypes.json";
import { fetchBySectionFmApi, fetchStudentsFmApi } from "../../http";
import { useModalStore } from "../../pinia/modal";
import { useSectionStore } from "../../pinia/section";
const { showModal, hideModal } = useModalStore();
const sterm = ref("");
const families = ref([]);
const students = ref([]);
const isLoading = ref(false);

const columns = ["DNI", "Apellidos y Nombre", "Hijos(as)", "Celular", "Telf.", "Acciones"];

const filtered = computed(() =>
    families.value.filter((item) => new RegExp(sterm.value, "i").test([item.person.name, item.person.lastname].join())),
);

async function fetchData() {
    const { code } = useSectionStore();
    isLoading.value = true;
    const { data } = await fetchBySectionFmApi(code);
    families.value = data.values;
    isLoading.value = false;
}

async function showStudents(item) {
    showModal("studentModal");
    if (item.students_count) {
        const { data } = await fetchStudentsFmApi(item.dni);
        students.value = data.values;
    }
}

onUnmounted(() => {
    hideModal("studentModal");
});
</script>
<template>
    <section>
        <card title="Registro de Apoderados">
            <template #rb>
                <m-router
                    :to="{ name: 'new_family' }"
                    size="btn-sm"
                    color="btn-inverse-info "
                >
                    Agregar
                </m-router>
            </template>
            <m-table
                v-model="sterm"
                :columns="columns"
                :data="filtered"
                :is-loading="isLoading"
                @refresh="fetchData"
            >
                <MySection @done="fetchData" />
                <template #data="{ rows }">
                    <tr
                        v-for="item in rows"
                        :key="item.dni"
                    >
                        <td>{{ item.dni }}</td>
                        <td>
                            <PersonLink
                                route="family_profile"
                                :person="item.person"
                            />
                        </td>
                        <td>
                            <b>{{ item.students_count }}</b>
                        </td>
                        <td>
                            <b>{{ item.person.phone }}</b>
                        </td>
                        <td>
                            <b>{{ item.telephone }}</b>
                        </td>
                        <td>
                            <m-button
                                size="btn-sm"
                                color="btn-inverse-info"
                                @ok="showStudents(item)"
                            >
                                Estudiantes
                            </m-button>
                        </td>
                    </tr>
                </template>
            </m-table>
            <template #foot>
                <footext :title="`${families.length} apoderados registrados`" />
            </template>
        </card>
        <Dialog
            id="studentModal"
            title="Estudiantes"
            btn-name="Cerrar"
        >
            <m-table
                :columns="['Parentezco:', 'Encargado', 'Estudiante']"
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
                    </tr>
                </template>
            </m-table>
            <template #foot>
                <span class="text-center mb-2 opacity-50">{{ students.length }} estudiantes</span>
            </template>
        </Dialog>
    </section>
</template>

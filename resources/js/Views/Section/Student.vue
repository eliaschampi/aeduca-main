<script setup>
import { computed, ref } from "vue";
import MySection from "../../Components/Views/MySection.vue";
import PersonLink from "../../Components/Views/PersonLink.vue";
import { downl } from "../../core/util";
import states from "../../data/states.json";
import { exportToExcelRegApi } from "../../http";
import { useRegisterStore } from "../../pinia/register";
import { useSectionStore } from "../../pinia/section";
const { fetchBySection } = useRegisterStore();
const isLoading = computed(() => useRegisterStore().isLoading);
const columns = [
    "#",
    "DNI",
    "Código",
    "Nombre y Apellidos",
    "Estado",
    "Turno",
    "Pago/Mens.",
    "Celular",
    "Últ. vez",
];
const prdecorated = {
    0: "Multiple",
    1: "Mañana",
    2: "Tarde",
    3: "Noche",
};

const sterm = ref("");
const showInactives = ref(false);
const isPrinting = ref(false);
const desactivateDw = computed(
    () => showInactives.value || isPrinting.value || !filtered.value.length,
);
const filtered = computed(() => {
    const { registers } = useRegisterStore();
    const rex = RegExp(sterm.value.toLowerCase(), "i");
    return registers.filter(({ student: { person } }) => {
        const student = [person.name, person.lastname].join().toLowerCase();
        return rex.test(student);
    });
});

async function exportToExcel() {
    const { section_code } = useSectionStore();
    isPrinting.value = true;
    try {
        const { data } = await exportToExcelRegApi(section_code);
        downl(data, `Registro-${section_code}`, ".xlsx");
    } finally {
        isPrinting.value = false;
    }
}
</script>
<template>
    <card title="Estudiantes por Sección">
        <template #rb>
            <m-button
                class="px-2"
                :disabled="desactivateDw"
                icon="icon ion-md-cloud-download icon-md"
                color="btn-warning btn-icon"
                size="btn-sm"
                @ok="exportToExcel"
            />
        </template>
        <m-table
            v-model="sterm"
            :columns="columns"
            :is-loading="isLoading"
            :data="filtered"
            @refresh="fetchBySection()"
        >
            <div class="d-flex align-items-center">
                <MySection @done="fetchBySection()" />
                <m-check
                    id="showinactives"
                    v-model="showInactives"
                    class="ms-3"
                    text="Mostrar pendientes e inactivos"
                    @update:model-value="fetchBySection"
                />
            </div>
            <template #data="{ rows }">
                <tr v-for="item in rows" :key="item.code">
                    <td class="my-0">
                        <img
                            v-if="item.student.person.profile"
                            class="rounded-circle img-thumbnail"
                            style="width: 2.5rem"
                            :src="`/default/${item.student.person.profile.image}`"
                        />
                    </td>
                    <td>{{ item.student_dni }}</td>
                    <td>{{ item.code }}</td>
                    <td>
                        <PersonLink :person="item.student.person" route="student_profile" />
                    </td>
                    <td>
                        <span class="badge" :class="[states[item.state].badge]">
                            {{ states[item.state].text }}
                        </span>
                    </td>
                    <td>{{ prdecorated[item.priority] }}</td>
                    <td>{{ $currency(item.monthly) }}</td>
                    <td>
                        <div class="d-flex">
                            <span class="icon ion-md-call text-success me-2"></span>
                            <b class="opacity-75">
                                {{ item.student.person.phone }}
                            </b>
                        </div>
                    </td>
                    <td>
                        <i v-if="item.student.person.profile" class="text-primary-emphasis">
                            {{ $ago(item.student.person.profile.last_logout) }}
                        </i>
                        <i v-else>Nunca</i>
                    </td>
                </tr>
            </template>
        </m-table>
        <template #foot>
            <footext :title="`${filtered.length} estudiantes matriculados`" />
        </template>
    </card>
</template>

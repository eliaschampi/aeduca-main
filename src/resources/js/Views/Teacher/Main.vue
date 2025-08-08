<script setup>
import { computed, ref, watchEffect } from "vue";
import { useRoute } from "vue-router";
import MainWrapper from "../../Components/Views/MainWrapper.vue";
import NavItem from "../../Components/Views/NavItem.vue";
import { downl } from "../../core/util";
import { changeStateTeacherApi, fetchCyclesApi, fetchTeacherApi, printCardApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useTeacherStore } from "../../pinia/teacher";
import { useUserStore } from "../../pinia/user";

const { successSnack } = useSnackbarStore();
const isLoading = ref(false);
const teacher = ref({});
const specialties = ref([]);
const routeName = useRoute().name;
const { updateImage } = useTeacherStore();
const storedDni = computed(() => useTeacherStore().dni);
const { can } = useUserStore();

async function fetchTeacher(dni) {
    const [sdata, tdata] = await Promise.all([fetchCyclesApi(), fetchTeacherApi(dni)]);
    specialties.value = sdata.data.values;
    teacher.value = tdata.data.value;
}

async function handleChangeState() {
    const { data } = await changeStateTeacherApi(storedDni.value);
    successSnack(data.message);
}

async function printCard() {
    if (isLoading.value) return;
    isLoading.value = true;
    const { data } = await printCardApi(storedDni.value, "teacher");
    downl(data, `Carnet ${teacher.value.fullname}`, ".pdf");
    isLoading.value = false;
}

watchEffect(() => {
    fetchTeacher(storedDni.value);
});

const attendanceShowable = computed(() => {
    const { branch_code, rol_code } = useUserStore();
    return teacher.value.branch_code === branch_code && rol_code !== "S";
});
</script>
<template>
    <MainWrapper
        ptype="teacher"
        @img-uploaded="updateImage"
    >
        <template #profile-info>
            <div class="border-rounded p-3 mx-auto mt-3">
                <div class="text-title title">
                    {{ specialties[teacher.specialty] }}
                </div>
                <p class="my-2"><b>Fecha de inicio:</b> {{ $date(teacher.startdate) }}</p>

                <m-switch
                    id="tcstate"
                    v-model="teacher.state"
                    :dis="!can('AN')"
                    :text="`¿Activo en ${new Date().getFullYear()}?`"
                    @update:model-value="handleChangeState"
                />
            </div>
        </template>
        <div class="card">
            <ul
                class="card-header nav nav-pills nav-mtab"
                role="tablist"
            >
                <NavItem
                    icon="ion-md-person"
                    title="Información"
                    route="teacher_profile"
                    :dni="storedDni"
                />
                <NavItem
                    icon="ion-ios-calendar"
                    title="Horario"
                    route="t_schedule"
                    :dni="storedDni"
                />
                <template v-if="attendanceShowable">
                    <NavItem
                        icon="ion-ios-checkmark-circle-outline"
                        title="Asistencia"
                        route="t_attendance"
                        :dni="storedDni"
                    />
                </template>
            </ul>
            <div class="card-body">
                <div
                    :id="routeName"
                    class="tab-pane fade show active"
                    role="tabpanel"
                >
                    <RouterView v-slot="{ Component }">
                        <Transition
                            name="fade"
                            mode="out-in"
                        >
                            <component :is="Component" />
                        </Transition>
                    </RouterView>
                </div>
            </div>
        </div>
        <template #profile-foot>
            <m-button
                v-can="'ANS'"
                :disabled="isLoading"
                color="btn-inverse-success"
                icon="icon ion-md-print"
                size="btn-sm"
                @ok="printCard"
            >
                {{ isLoading ? "Espere..." : "Carnet" }}
            </m-button>
        </template>
    </MainWrapper>
</template>

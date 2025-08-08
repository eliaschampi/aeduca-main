<script setup>
import { computed, inject, onMounted, onUnmounted, ref } from "vue";
import PersonType from "../../Components/Views/PersonType.vue";
import { changeBranchStApi, changeDNIApi, delPersonApi, fetchAllBranchApi } from "../../http";
import axios from "../../http/axios.js";
import { useDNIStore } from "../../pinia/dni";
import { useModalStore } from "../../pinia/modal";
import { useRefreshStore } from "../../pinia/refresh";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
import DestroyPerson from "./components/DestroyPerson.vue";
import UpdateBranch from "./components/UpdateBranch.vue";
import UpdateDNI from "./components/UpdateDNI.vue";
const { successSnack } = useSnackbarStore();
const { branch_code } = useUserStore();
const { hideModal } = useModalStore();
const dni = ref(useDNIStore().dni);
const message = ref("");
const type = ref("student");
const person = ref(null);
const action = ref("");
const branches = ref([]);
const decom = ref(null);
const emitter = inject("emitter");
const actionComponent = computed(() => {
    if (action.value === "updateDni") return UpdateDNI;
    if (action.value === "updateBranch") return UpdateBranch;
    if (action.value === "destroyPerson") return DestroyPerson;
    return null;
});

const branchable = computed(() => {
    if (!person.value) return false;
    return person.value.type === "student";
});

const branchtext = computed(() => {
    if (!person.value) return null;
    if (!branches.value.length) return null;
    const { branch } = person.value;
    return (branches.value.find((item) => item.code === branch) || {}).name;
});

const persondni = computed(() => {
    if (!person.value) return "";
    return person.value.dni;
});

async function findPerson() {
    message.value = "";
    if (!/^\d{8}$/.test(dni.value)) {
        message.value = "Ingrese un dni correcto";
        person.value = null;
        return;
    }
    if (person.value !== null && person.value.dni === dni.value) return;
    try {
        const { data } = await axios.get(`${type.value}/${dni.value}`);
        person.value = {
            dni: dni.value,
            fullname: `${data.value.person.name} ${data.value.person.lastname}`,
            type: type.value,
            branch: data.value.branch_code,
        };
        if (branchable.value) await fetchBranches();
    } catch {
        person.value = null;
    }
}

async function fetchBranches() {
    if (branches.value.length === 0) {
        const { data } = await fetchAllBranchApi();
        branches.value = data.values;
    }
}

function handleContinue(mode = "show") {
    hideModal("reauth");
    decom.value.handleShowModal(mode);
}

function afterSuccess(message) {
    successSnack(message);
    person.value = null;
    handleContinue("hide");
}

async function handleDeleteClick() {
    const { data } = await delPersonApi(person.value.dni);
    afterSuccess(data.message);
}

async function handleBranchClick(payload) {
    const { data } = await changeBranchStApi(person.value.dni, payload);
    afterSuccess(data.message);
}

async function handleDNIClick(newdni, err) {
    try {
        const { data } = await changeDNIApi(person.value.dni, newdni);
        afterSuccess(data.message);
    } catch (error) {
        if (error.code) {
            err(error.message);
        }
    }
}

onMounted(() => {
    emitter.on("reauthed", handleContinue);
});

onUnmounted(() => {
    emitter.off("reauthed", handleContinue);
});

function reAuthenticate() {
    const { showRefresh } = useRefreshStore();
    showRefresh("Se requiere autenticación", "/check");
}
</script>
<template>
    <section
        id="aec"
        style="max-width: 40rem"
    >
        <panel title="Consultas avanzadas">
            <blockquote class="blockquote">
                Con este módulo, podrás actualizar o eliminar la información completa de cualquier entidad, ya sea
                estudiante, docente o apoderado.
                <i class="text-info-emphasis">
                    Por razones de seguridad, esta actividad será notificada y monitoreada
                </i>
            </blockquote>
            <PersonType v-model="type" />
            <div class="d-flex align-items-center space my-3">
                <div class="form-floating">
                    <input
                        id="finderid"
                        v-model="dni"
                        class="form-control"
                        placeholder="Ejm. 15481548"
                        maxlength="8"
                        @keypress.enter="findPerson"
                    >
                    <label
                        class="form-label"
                        for="finderid"
                    >Ingresa el DNI</label>
                </div>
                <m-action
                    v-show="dni.length === 8"
                    icon="search"
                    color="info"
                    tool="buscar"
                    @action="findPerson"
                />
            </div>
            <alert
                v-show="message"
                type="alert-danger mt-3"
            >
                {{ message }}
            </alert>
            <template v-if="person">
                <m-plain
                    label="DNI:"
                    :value="person.dni"
                />
                <m-plain
                    label="Nombres:"
                    :value="person.fullname"
                />
                <m-plain
                    label="Sede:"
                    :value="branchtext"
                />
                <RouterLink
                    v-show="person.branch === branch_code"
                    class="btn btn-outline-success mb-2"
                    :to="{ name: `${person.type}_profile`, params: { dni: person.dni } }"
                >
                    Ir al perfil
                </RouterLink>
                <div
                    class="border-rounded p-3 mt-3"
                    style="max-width: 25rem"
                >
                    <div class="text-danger title">Zona de peligro</div>
                    <div class="d-flex space align-items-center">
                        <select
                            v-model="action"
                            class="form-select"
                        >
                            <option
                                value=""
                                disabled
                                hidden
                                selected
                            >
                                Seleccione un acción
                            </option>
                            <option value="updateDni">Actualizar DNI</option>
                            <option
                                value="updateBranch"
                                :disabled="!branchable"
                            >
                                Cambiar de sede
                            </option>
                            <option value="destroyPerson">Eliminar permanentemente</option>
                        </select>
                        <m-button
                            :disabled="dni.length !== 8 || !action"
                            color="btn-inverse-success"
                            @ok="reAuthenticate"
                        >
                            Continuar
                        </m-button>
                    </div>
                </div>
            </template>
        </panel>
        <component
            :is="actionComponent"
            ref="decom"
            :branches="branches"
            :dni="persondni"
            @okdelete="handleDeleteClick"
            @mchangeb="handleBranchClick"
            @udni="handleDNIClick"
        />
    </section>
</template>
<style scoped>
.inputw {
    max-width: 15rem;
}
</style>

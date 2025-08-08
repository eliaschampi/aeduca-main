<script setup>
import { inject, onMounted, onUnmounted, ref } from "vue";
import IncomeDetail from "../../Components/Views/IncomeDetail.vue";
import InputFinder from "../../Components/Views/InputFinder.vue";
import states from "../../data/states.json";
import { fetchRegistersApi, fetchFullnameApi, fetchPaidsPyApi } from "../../http";
import { useCatStore } from "../../pinia/cat";
import { useDetailStore } from "../../pinia/detail";
import { useModalStore } from "../../pinia/modal";
import { useRegisterStore } from "../../pinia/register";
import { useStudentStore } from "../../pinia/student";
import CashReport from "./components/CashReport.vue";
import SetupReport from "./components/SetupReport.vue";
import { useRoute, useRouter } from "vue-router";
const { hideModal, showModal } = useModalStore();
const { setCanceled, setDetail, setIsNew } = useDetailStore();
const selected = ref({});
const student_name = ref("");
const tab = ref("setup");
const paids = ref([]);
const registers = ref([]);
const emitter = inject("emitter");
const route = useRoute();
const router = useRouter();

async function fetchRegisters(dni) {
    const { data } = await fetchRegistersApi(dni);
    registers.value = data.values;
}

async function fetchStudent(dni) {
    const { data } = await fetchFullnameApi(dni);
    if (data.value) {
        student_name.value = `${data.value.name} ${data.value.lastname}`;
        fetchRegisters(route.params.dni);
    }
}

async function onPersonSelected({ dni, name, lastname }) {
    selected.value = {};
    fetchRegisters(dni);
    student_name.value = `${name} ${lastname}`;
    hideModal("finderModal");
    if (dni !== route.params.dni) {
        router.push({ name: route.name, params: { dni } });
    }
}

function registerFullName({ year, section_code, level }) {
    return `${year} - ${section_code.substr(-2)} de ${level}`;
}

function handlePay(action_type_code, amount, title) {
    const { setRegister } = useRegisterStore();
    const { cats } = useCatStore();
    const actiontype = cats.find((item) => item.code === action_type_code);
    setRegister(selected.value);
    setDetail({
        actiontype: actiontype || "",
        topay: amount,
        paid: amount,
        month: title,
    });
    showModal("invoiceModal");
}

function cancelPay(item) {
    setIsNew(false);
    setCanceled(true);
    const balance = item.topay - item.paid - item.discount;
    handlePay(item.actiontype.code, balance, item.title);
    showModal("invoiceModal");
}

function realizePay(item) {
    setIsNew(false);
    handlePay(item.pcode, item.amount, item.description);
    showModal("invoiceModal");
}

function setStudentAndRegister() {
    const { dni, fullname } = useStudentStore();
    if (/^\d{8}$/.test(route.params.dni)) {
        if (dni === route.params.dni) {
            onPersonSelected({ dni, name: fullname, lastname: "" });
        } else {
            fetchStudent(route.params.dni);
        }
    } else {
        if (dni) {
            router.push({ name: route.name, params: { dni } });
        }
    }
}

async function fetchPaids(register_code) {
    const { data } = await fetchPaidsPyApi(register_code);
    paids.value = data.values;
}

function handleShowPaids() {
    tab.value = "cash";
    if (!paids.value.length && selected.value.code) {
        fetchPaids(selected.value.code);
    }
}

function handleChangeReg() {
    if (tab.value === "cash") {
        fetchPaids(selected.value.code);
    }
}

onMounted(() => {
    const { fetchCats } = useCatStore();
    fetchCats();
    setStudentAndRegister();
    emitter.on("afterSelectPerson", onPersonSelected);
});

onUnmounted(() => {
    emitter.off("afterSelectPerson", onPersonSelected);
});
</script>
<template>
    <section>
        <panel title="Estudiante">
            <div class="d-flex align-items-center flex-wrap space">
                <InputFinder
                    who="student"
                    style="max-width: 20rem"
                    :only-current-reg="false"
                    :only-current-branch="false"
                    :fullname="student_name"
                />
                <m-select
                    v-model="selected"
                    style="max-width: 15rem"
                    name="Mat"
                    class="resmar"
                    :options="registers"
                    label="Matriculas"
                    opt-value="all"
                    :opt-label="registerFullName"
                    standalone
                    @update:model-value="handleChangeReg"
                />
                <template v-if="selected.code">
                    <div class="resmar">
                        <b class="fw-medium">Mes: </b>
                        <span>{{ $currency(selected.monthly) }}</span>
                    </div>
                    <div class="resmar">
                        <b class="fw-medium me-1">Estado:</b>
                        <span
                            class="badge"
                            :class="states[selected.state].badge"
                        >
                            {{ states[selected.state].text }}
                        </span>
                    </div>
                </template>
            </div>
        </panel>
        <card
            title="Gestión de pagos"
            :f="false"
            class="mt-4"
        >
            <template #rb>
                <template v-if="selected.code && tab === 'setup'">
                    <m-button
                        size="btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#apmodal"
                    >
                        Agregar
                    </m-button>
                </template>
            </template>
            <ul class="nav nav-line nav-fill">
                <li
                    class="nav-item"
                    role="presentation"
                    @click="tab = 'setup'"
                >
                    <button
                        id="setupPays-tab"
                        class="nav-link active"
                        data-bs-toggle="tab"
                        data-bs-target="#setupPays"
                        type="button"
                        role="tab"
                        aria-controls="setupPays"
                        aria-selected="true"
                    >
                        Pagos establecidos
                    </button>
                </li>
                <li
                    class="nav-item"
                    role="presentation"
                    @click="handleShowPaids"
                >
                    <button
                        id="cashPays-tab"
                        class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#cashPays"
                        type="button"
                        role="tab"
                        aria-controls="cashPays"
                        aria-selected="false"
                    >
                        Pagos en caja
                    </button>
                </li>
            </ul>
            <div
                id="repContent"
                class="tab-content mt-3"
            >
                <div
                    id="setupPays"
                    class="tab-pane fade show active p-2 space-b"
                    role="tabpanel"
                    aria-labelledby="setupPays-tab"
                >
                    <SetupReport
                        :register-code="selected.code"
                        :monthly="selected.monthly"
                        @cancel="realizePay"
                    />
                </div>
                <div
                    id="cashPays"
                    class="tab-pane fade p-2 space-b"
                    role="tabpanel"
                    aria-labelledby="cashPays-tab"
                >
                    <CashReport
                        :paids="paids"
                        @cancel="cancelPay"
                    />
                </div>
            </div>
        </card>
        <IncomeDetail @cancel="setCanceled(false)" />
    </section>
</template>

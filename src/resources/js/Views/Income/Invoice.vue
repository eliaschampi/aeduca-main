<script setup>
import { computed, inject, onMounted, onUnmounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import IncomeDetail from "../../Components/Views/IncomeDetail.vue";
import InputFinder from "../../Components/Views/InputFinder.vue";
import { ticket } from "../../core/ticket.js";
import {
    cleanCachedIncomeApi,
    fetchCachedDetailApi,
    fetchRegistersApi,
    fetchSimpleCashApi,
    removeCachedDetailApi,
    storeIncomeApi,
} from "../../http";
import { useDetailStore } from "../../pinia/detail";
import { useModalStore } from "../../pinia/modal";
import { useRegisterStore } from "../../pinia/register";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useStudentStore } from "../../pinia/student";
import { useUserStore } from "../../pinia/user";
import Customer from "../Customer/Modal.vue";
const { hideModal } = useModalStore();
const { successSnack, showSnack, warningSnack } = useSnackbarStore();
const { updateDetailByField } = useDetailStore();
const emitter = inject("emitter");
const router = useRouter();
const isLoading = ref(false);
const student_name = ref("Buscar estudiante");
const cachedRegister = ref(null);
const registers = ref([]);
const invoice = reactive({
    customer_identifier: "",
    section_code: "",
    cash_code: "",
    mod: "student",
    type: "00",
    details: [],
});
const errMessages = ref([]);
const customer = reactive({
    name: "",
});
const columns = ["#", "Modalidad de Ingreso", "Concepto", "Monto a pagar", "Descuento", "Importe", "Saldo", "#"];

const discount = computed(() => {
    return invoice.details.reduce((total, item) => {
        return total + parseFloat(item.discount);
    }, 0);
});

const paid = computed(() => {
    return invoice.details.reduce((total, item) => {
        return total + parseFloat(item.paid);
    }, 0);
});

const pending = computed(() => {
    const topay = invoice.details.reduce((total, item) => {
        return total + parseFloat(item.topay);
    }, 0);
    return topay - discount.value - paid.value;
});

async function handleGetCash() {
    const { data } = await fetchSimpleCashApi();
    if (data.value && data.value.state) {
        invoice.cash_code = data.value.code;
    }
}

async function fetchCachedDetail() {
    const { data } = await fetchCachedDetailApi();
    invoice.details = data.values;
    if (data.register !== false) {
        cachedRegister.value = data.register;
        invoice.mod = "student";
        invoice.section_code = data.register.section_code;
    } else {
        const { dni, fullname } = useStudentStore();
        if (dni) {
            const { register } = useRegisterStore();
            if (dni === register.student_dni) {
                student_name.value = fullname;
                const { data } = await fetchRegistersApi(dni);
                registers.value = data.values;
                invoice.customer_identifier = register.code;
            }
        }
    }
}

async function onPersonSelected({ dni, name, lastname }) {
    const { data } = await fetchRegistersApi(dni);
    registers.value = data.values;
    invoice.customer_identifier = "";
    student_name.value = `${name} ${lastname}`;
    hideModal("finderModal");
}

function registerFullName({ year, section_code, level }) {
    return `${year} - ${section_code.substr(-2)} de ${level}`;
}

function cleanAfter() {
    useRegisterStore().setRegister(null);
    useStudentStore().setPerson(null);
}

function isValid() {
    errMessages.value = [];
    if (invoice.details.length === 0) {
        errMessages.value.push("Falta al menos un registro de ingreso");
        return false;
    }
    if (invoice.mod === "customer") {
        if (customer.name === "") {
            errMessages.value.push("Falta elegir un cliente");
            return false;
        }
    } else if (!invoice.customer_identifier && !cachedRegister.value) {
        errMessages.value.push("Falta elegir un Estudiante");
        return false;
    }

    if (invoice.cash_code === "") {
        errMessages.value.push("Falta aperturar la caja");
        return false;
    }
    return true;
}

async function handlePrintTicket(income) {
    const client = { name: "" };
    if (invoice.mod === "customer") {
        invoice.customer_identifier = customer;
        client.name = customer.name;
    } else {
        client.name = !cachedRegister.value ? student_name.value : cachedRegister.value.student_name;
    }
    await ticket(
        {
            ...income,
            user: {
                name: useUserStore().user.name,
            },
            name: client,
        },
        invoice.details,
    );
}

function handleSubmitClick() {
    if (!isValid()) return;
    invoice.total_discount = discount.value;
    invoice.total = paid.value;
    if (invoice.mod === "customer") {
        invoice.customer_identifier = customer;
    }
    showSnack({
        text: "El comprobante será registrado",
        button: "confirmar",
        action: async () => {
            try {
                isLoading.value = true;
                const { data } = await storeIncomeApi(invoice);
                successSnack(data.message);
                cleanAfter();
                handlePrintTicket(data.income);
                router.push({ name: "cash" });
            } catch {
                warningSnack("Ocurrio un error, comunicate con el soporte");
            } finally {
                isLoading.value = false;
            }
        },
    });
}

function afterAddDetail(payload) {
    invoice.details = payload.values;
    successSnack(payload.message);
}

async function handleDeleteItem(item) {
    const { data } = await removeCachedDetailApi(item.id);
    invoice.details.splice(invoice.details.indexOf(item), 1);
    successSnack(data.message);
}

function customerSelected(val) {
    Object.assign(customer, val);
    hideModal("customerModal");
}

function handleRegSelect() {
    const item = registers.value.find((reg) => reg.code === invoice.customer_identifier);
    updateDetailByField({ field: "topay", value: item.monthly });
}

function onClearCustomer() {
    Object.assign(customer, { name: "", code: "" });
}

function handleCancel() {
    showSnack({
        text: "¿Esta seguro de limpiar todos los registros?",
        button: "Confirmar",
        action: async () => {
            const { data } = await cleanCachedIncomeApi();
            successSnack(data.message);
            location.reload();
        },
    });
}

onMounted(() => {
    handleGetCash();
    fetchCachedDetail();
    emitter.on("afterSelectPerson", onPersonSelected);
});

onUnmounted(() => {
    emitter.off("afterSelectPerson", onPersonSelected);
});
</script>
<template>
    <section id="invoice">
        <div class="row justify-content-end">
            <panel
                v-if="cachedRegister"
                class="col-md-8 mb-4"
            >
                <alert :dismisable="false">
                    <i
                        class="icon ion-md-megaphone"
                        aria-hidden="true"
                    />
                    Hay una matrícula en proceso del estudiante
                    <b class="fw-medium">{{ cachedRegister.student_name }}</b>
                    Por lo tanto, este comprobante será registrado con esta razón social.
                    <b
                        class="fw-medium pointer"
                        @click="handleCancel"
                    > Para cancelar este proceso clic aquí </b>
                </alert>
            </panel>
            <div
                v-else
                class="col-md-8"
            >
                <panel>
                    <div class="selectgroup selectgroup-pills mb-3">
                        <label class="selectgroup-item">
                            <input
                                v-model="invoice.mod"
                                type="radio"
                                name="mode_in"
                                value="student"
                                class="selectgroup-input"
                            >
                            <span class="selectgroup-button">Estudiante</span>
                        </label>
                        <label class="selectgroup-item">
                            <input
                                v-model="invoice.mod"
                                type="radio"
                                name="mode_in"
                                value="customer"
                                class="selectgroup-input"
                            >
                            <span class="selectgroup-button">Cliente</span>
                        </label>
                    </div>
                    <div
                        v-if="invoice.mod === 'student'"
                        class="d-flex align-items-center flex-wrap space"
                    >
                        <InputFinder
                            who="student"
                            style="max-width: 20rem"
                            :only-current-reg="false"
                            :only-current-branch="false"
                            :fullname="student_name"
                        />
                        <m-select
                            v-model="invoice.customer_identifier"
                            style="width: 12rem"
                            name="Mat"
                            :options="registers"
                            label="Matriculas"
                            :opt-label="registerFullName"
                            standalone
                            @update:model-value="handleRegSelect"
                        />
                    </div>
                    <!-- customer -->
                    <template v-else>
                        <div style="max-width: 25rem">
                            <div class="input-group">
                                <input
                                    v-model="customer.name"
                                    class="form-control"
                                    placeholder="Ingrese una razón social"
                                >
                                <m-button
                                    data-bs-toggle="modal"
                                    data-bs-target="#customerModal"
                                    size="btn-sm"
                                >
                                    Frecuentes
                                </m-button>
                            </div>
                        </div>
                        <i
                            v-show="customer.code"
                            class="text-primary pointer mt-2"
                            @click="onClearCustomer"
                        >
                            Quitar Cliente frecuente
                        </i>
                    </template>
                </panel>
            </div>
            <div class="col-md-4 mt-4 mt-md-0 mb-4">
                <panel class="text-center">
                    <h5>
                        <i class="icon ion-ios-wallet text-success" /> CAJA:
                        <RouterLink :to="{ name: 'cash' }">
                            {{ invoice.cash_code }}
                            <span v-show="invoice.cash_code === ''"> ABRIR AHORA </span>
                        </RouterLink>
                    </h5>
                    <hr>
                    <div class="m-2 form-floating">
                        <select
                            id="voucher_type_select"
                            v-model="invoice.type"
                            class="form-select form-select-lg"
                            name="voucher_type_select"
                        >
                            <option value="00">Nota de Venta</option>
                            <option value="03">Boleta de Venta</option>
                        </select>
                        <label for="voucher_type_select">Tipo de comprobante</label>
                    </div>
                </panel>
            </div>
        </div>
        <panel :f="true">
            <div class="row p-3">
                <div class="col-lg-9">
                    <div class="d-flex space">
                        <m-button
                            color="btn-inverse-success"
                            class="btn-icon"
                            icon="ion-md-add"
                            data-bs-toggle="modal"
                            data-bs-target="#invoiceModal"
                        />
                        <m-button
                            color="btn-inverse-warning"
                            icon="icon ion-md-refresh"
                            :disabled="!invoice.details.length"
                            @ok="handleCancel"
                        >
                            Nuevo
                        </m-button>
                    </div>
                    <m-table
                        :columns="columns"
                        :data="invoice.details"
                        :head="false"
                    >
                        <template #data="{ rows }">
                            <tr
                                v-for="item in rows"
                                :key="item.id"
                            >
                                <td>{{ item.id }}</td>
                                <td>{{ item.actiontype.name }}</td>
                                <td>{{ item.title }}</td>
                                <td>{{ $currency(item.topay) }}</td>
                                <td>{{ $currency(item.discount) }}</td>
                                <td>{{ $currency(item.paid) }}</td>
                                <td>{{ $currency(item.pending) }}</td>
                                <td>
                                    <m-action
                                        color="danger"
                                        icon="remove-circle"
                                        tool="Excluir"
                                        @action="handleDeleteItem(item)"
                                    />
                                </td>
                            </tr>
                        </template>
                    </m-table>
                    <alert
                        v-show="errMessages.length"
                        type="alert-danger"
                        :dismisable="false"
                    >
                        <div
                            v-for="(item, index) in errMessages"
                            :key="index"
                        >
                            <i class="icon ion-md-info" /> {{ item }}
                        </div>
                    </alert>
                </div>
                <div class="col-md-12 col-lg-3">
                    <div class="p-3 myround shadow-sm border-rounded d-flex justify-content-between fw-bold">
                        <div class="text-title space-b">
                            <div>Descuento:</div>
                            <div>Importe Total:</div>
                            <div>Saldo Pendiente:</div>
                        </div>
                        <div class="space-b">
                            <div>{{ $currency(discount) }}</div>
                            <div>{{ $currency(paid) }}</div>
                            <div>{{ $currency(pending) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <template #foot>
                <footext>
                    <m-button
                        icon="ion-md-cart"
                        size="btn-lg"
                        color="btn-inverse-success"
                        @ok="handleSubmitClick"
                    >
                        Finalizar
                    </m-button>
                </footext>
            </template>
        </panel>
        <Customer @selected="customerSelected" />
        <IncomeDetail
            :is-new="true"
            @ok="afterAddDetail"
        />
    </section>
</template>

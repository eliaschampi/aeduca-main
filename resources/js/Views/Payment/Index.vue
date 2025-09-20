<script setup>
import { computed, onMounted, ref } from "vue";
import IncomeDetail from "../../Components/Views/IncomeDetail.vue";
import MySection from "../../Components/Views/MySection.vue";
import { dformat } from "../../core/day";
import { fetchDebtsRegApi, toggleStateRegApi } from "../../http";
import { useCatStore } from "../../pinia/cat";
import { useDetailStore } from "../../pinia/detail";
import { useModalStore } from "../../pinia/modal";
import { useSectionStore } from "../../pinia/section";
import { useSnackbarStore } from "../../pinia/snackbar";
import SetupNew from "./components/SetupNew.vue";
import months from "../../data/months.json";
import PersonLink from "../../Components/Views/PersonLink.vue";

const { successSnack } = useSnackbarStore();
const { showModal } = useModalStore();
const catStore = useCatStore();
const columns = [
    "Estudiante",
    "Estado",
    "Monto a pagar",
    "Fecha de pago",
    "Fecha limite",
    "Realizar pago",
];
const searchTerm = ref("");
const registers = ref([]);
const registerCode = ref(null);
const isLoading = ref(false);
const filtered = computed(() =>
    registers.value.filter((item) => new RegExp(searchTerm.value, "i").test(item.name)),
);
const addPaymentModal = ref(null);
const selectedMonth = ref("");
const caseLabel = {
    Vencido: "badge bg-danger-subtle text-danger-emphasis border border-danger border-2",
    Pagado: "badge bg-success-subtle text-success-emphasis border border-success border-2",
    Pendiente: "badge bg-warning-subtle text-warning-emphasis border border-warning border-2",
};

const formatedDate = (date) => dformat(date, "dddd DD MMM");

async function fetchData() {
    isLoading.value = true;
    const { section_code } = useSectionStore();
    const { data } = await fetchDebtsRegApi(section_code, selectedMonth.value);
    registers.value = data.values;
    isLoading.value = false;
}

async function handleDeactiveState(item) {
    const state = item.state === "a" ? "i" : "a";
    const { data } = await toggleStateRegApi(item.register_code, state);
    successSnack(data.message);
    item.state = state;
}

async function handleShowPaymentModal(item) {
    registerCode.value = item.register_code;
    if (!catStore.cats.length) {
        await catStore.fetchCats();
    }
    addPaymentModal.value.handleShowModal(item);
}

const handlePaymentUpdated = (code) => {
    const item = registers.value.find((r) => r.code === code);
    item.pstate = "Pagado";
    item.paid = new Date().toISOString();
};

function handlePayment({ pcode, amount, description }) {
    const { setDetail } = useDetailStore();
    setDetail({
        actiontype: catStore.cats.find((item) => item.code === pcode),
        topay: amount,
        paid: amount,
        month: description,
    });
    showModal("invoiceModal");
}

onMounted(() => {
    const currentMonth = (new Date().getMonth() + 1).toString().padStart(2, "0");
    selectedMonth.value = months.find((m) => m.code === currentMonth).name;
});
</script>

<template>
    <section>
        <panel title="Pagos pendientes" :f="false">
            <hr style="opacity: 15%" />
            <m-table
                v-model="searchTerm"
                :columns="columns"
                :data="filtered"
                :is-loading="isLoading"
                @refresh="fetchData"
            >
                <div class="d-flex space">
                    <m-select
                        v-model="selectedMonth"
                        name="cat"
                        :options="months"
                        standalone
                        opt-value="name"
                        style="max-width: 8rem"
                        @update:model-value="fetchData"
                    />
                    <MySection @done="fetchData" />
                </div>
                <template #data="{ rows }">
                    <template v-for="item in rows" :key="item.register_code">
                        <tr>
                            <td>
                                <PersonLink :person="item" route="payment" />
                            </td>
                            <td>
                                <small :class="caseLabel[item.pstate]">
                                    <b>{{ item.pstate }}</b>
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center space">
                                    <div class="boli bg-primary"></div>
                                    <span>{{ $currency(item.amount) }}</span>
                                </div>
                            </td>
                            <td>
                                <b v-if="item.paid">
                                    <em>{{ formatedDate(item.paid) }}</em>
                                </b>
                            </td>
                            <td>
                                <b>
                                    <em>{{ formatedDate(item.topay) }}</em>
                                </b>
                            </td>
                            <td>
                                <div v-year class="space">
                                    <m-action
                                        v-show="/(a|i)/.test(item.state)"
                                        :icon="`icon ion-md-${item.state === 'a' ? 'lock' : 'unlock'}`"
                                        :color="item.state === 'a' ? 'danger' : 'success'"
                                        tool="Desactivar acceso"
                                        @action="handleDeactiveState(item)"
                                    />
                                    <template v-if="!item.paid">
                                        <m-action
                                            icon="checkmark"
                                            color="info-emphasis"
                                            tool="Actualizar"
                                            @action="handleShowPaymentModal(item)"
                                        />
                                        <m-action
                                            icon="cart"
                                            color="primary-emphasis"
                                            tool="Pagar en caja"
                                            @action="handlePayment(item)"
                                        />
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>
            </m-table>
            <template #foot>
                <footext :title="`${registers.length} estudiantes`" />
            </template>
        </panel>
        <SetupNew
            ref="addPaymentModal"
            :cats="catStore.cats"
            :register-code="registerCode"
            @after-add="handlePaymentUpdated"
        />
        <IncomeDetail />
    </section>
</template>

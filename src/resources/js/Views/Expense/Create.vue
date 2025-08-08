<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { fetchCatApi, fetchExApi, fetchSimpleCashApi, storeExApi, updateExApi } from "../../http";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
import Detail from "./components/Detail.vue";
const router = useRouter();
const { successSnack, dangerSnack } = useSnackbarStore();
const { hideModal } = useModalStore();
const expense = reactive({
    detail: [],
    cash_code: "",
    total: 0,
    description: "",
    voucher_num: "",
});
const types = ref([]);
const cash = ref(0);
const expenseCode = useRoute().params.code;
const isLoading = ref(false);
const hasDetail = ref(false);

async function fetchCash() {
    const {
        data: { value },
    } = await fetchSimpleCashApi();
    if (value && value.state) {
        expense.cash_code = value.code;
        cash.value = value.cash;
    }
}

async function fetchCats() {
    const { data } = await fetchCatApi("Egreso");
    types.value = data.values;
}

async function fetchExByCode() {
    try {
        const { data } = await fetchExApi(expenseCode);
        Object.assign(expense, data.value);
        hasDetail.value = expense.detail.length > 0;
    } catch {
        router.push({ name: "expense" });
    }
}

function calculateTotal() {
    expense.total = expense.detail.reduce((acu, { unit_price, quantity }) => acu + unit_price * quantity, 0);
}

function handleAddDetail(payload) {
    expense.detail.push({ ...payload });
    calculateTotal();
    hideModal("expenseDetailModal");
}

function handleRemoveDetail(payload) {
    expense.detail.splice(expense.detail.indexOf(payload), 1);
    calculateTotal();
}

async function handleSubmitClick() {
    if (hasDetail.value && !expense.detail.length) {
        dangerSnack("Falta registrar al menos un detalle");
        return;
    }
    isLoading.value = true;
    const store = () => (expenseCode ? updateExApi(expense) : storeExApi(expense));
    const { data } = await store();
    successSnack(data.message);
    router.push({ name: "expense" });
    isLoading.value = false;
}

onMounted(async () => {
    await fetchCats();
    await fetchCash();
    if (expenseCode && !isNaN(expenseCode)) {
        await fetchExByCode();
    }
});

const schema = computed(() => ({
    cashactiontype_code: {
        required: true,
    },
    description: {
        required: true,
        min: 10,
        max: 150,
    },
    voucher_num: {
        max: 20,
    },
    cash_code: {
        required: true,
    },
    total: {
        required: true,
        natural_min: 0,
        natural_max: cash.value,
    },
}));
</script>
<template>
    <section id="NewExpense">
        <m-form
            :title="expenseCode ? 'Registrar un gasto' : 'Modificar gasto'"
            :schema="schema"
            btn-name="Guardar"
            :is-loading="isLoading"
            @ok="handleSubmitClick"
        >
            <div class="row gx-2">
                <div class="col-md-6 space-b-1">
                    <m-select
                        v-model="expense.cashactiontype_code"
                        name="cashactiontype_code"
                        label="Tipo de Gasto"
                        :options="types"
                    />
                    <blockquote class="blockquote">
                        <b>Importante.</b> El campo Concepto tiene que estar bien descrito
                        <i class="text-info-emphasis">
                            Si el gasto tiene detalles active la opción "Registrar detalle"
                        </i>
                    </blockquote>
                    <m-textarea
                        v-model="expense.description"
                        name="description"
                        label="Concepto"
                    />
                    <m-input
                        v-model="expense.voucher_num"
                        name="voucher_num"
                        label="N° de Comprobante"
                    />
                </div>
                <div class="col-md-6 space-b-1">
                    <m-input
                        :model-value="expense.cash_code"
                        label="Caja"
                        name="cash_code"
                        disabled
                    />
                    <m-switch
                        id="hfwerf"
                        v-model="hasDetail"
                        :dis="!!expenseCode"
                        text="Registrar detalle de comprobante en lugar de un total"
                    />
                    <m-input
                        v-show="!hasDetail"
                        v-model="expense.total"
                        name="total"
                        label="Total"
                        type="number"
                        step="0.01"
                    />
                    <template v-if="hasDetail">
                        <m-button
                            class="my-2"
                            data-bs-toggle="modal"
                            data-bs-target="#expenseDetailModal"
                            size="btn-sm"
                        >
                            Agregar
                        </m-button>
                        <m-table
                            :columns="['Descripción', 'Precio', 'Cantidad', 'Total']"
                            :data="expense.detail"
                            :head="false"
                            emptytext="Agrega los detalles del gasto"
                        >
                            <template #data="{ rows }">
                                <tr
                                    v-for="(item, index) in rows"
                                    :key="index"
                                >
                                    <th>{{ item.description }}</th>
                                    <th>{{ $currency(item.unit_price) }}</th>
                                    <th>{{ item.quantity }}</th>
                                    <th>
                                        {{ $currency(item.unit_price * item.quantity) }}
                                    </th>
                                    <th>
                                        <span
                                            v-if="!item.code"
                                            class="text-primary pointer"
                                            @click="handleRemoveDetail(item)"
                                        >
                                            <i class="icon ion-md-remove-circle icon-md" />
                                        </span>
                                    </th>
                                </tr>
                            </template>
                            <template #foot>
                                <div class="space">
                                    <i class="text-warning">Total: {{ $currency(expense.total) }}</i>
                                    <i
                                        v-show="expense.total > cash"
                                        class="icon ion-md-information-circle text-danger"
                                    >
                                        El total no debe superar a la caja
                                    </i>
                                </div>
                            </template>
                        </m-table>
                    </template>
                </div>
            </div>
        </m-form>
        <Detail @ok="handleAddDetail" />
    </section>
</template>

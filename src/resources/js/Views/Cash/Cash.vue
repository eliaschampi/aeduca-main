<script setup>
import { computed, inject, onMounted, onUnmounted, ref } from "vue";
import { useRouter } from "vue-router";
import Casher from "../../Components/Views/Casher.vue";
import Datepick from "../../Components/Views/Datepick.vue";
import { iso } from "../../core/date.js";
import { dformat } from "../../core/day";
import { fetchCashApi, openCashApi, toggleCashApi } from "../../http";
import { useCasherStore } from "../../pinia/casher";
import { useDateStore } from "../../pinia/date";
import { useModalStore } from "../../pinia/modal";
import { useRefreshStore } from "../../pinia/refresh";
import { useSnackbarStore } from "../../pinia/snackbar";
import NewCash from "./components/NewCash.vue";
import Surrender from "./components/Surrender.vue";
import Widget from "./components/Widget.vue";
const store = useDateStore();
const cash = ref(null);
const router = useRouter();
const emitter = inject("emitter");
const { successSnack, warningSnack } = useSnackbarStore();
const { showModal, hideModal } = useModalStore();
const lastCash = ref("0.00");

const cash_closed = computed(() => {
    if (cash.value === null) return true;
    return store.isToday ? !cash.value.state : false;
});

const cash_icon = computed(() => {
    if (cash.value === null) return "icon ion-md-log-in icon-lg text-primary";
    if (cash.value.state) {
        return "icon ion-md-unlock icon-lg text-success";
    }
    return "icon ion-md-lock icon-lg text-primary";
});

const hasSur = computed(() => {
    if (cash.value.code) {
        return cash.value.surrendered !== null;
    }
    return false;
});

const initial = computed(() => {
    let sum = Number(cash.value.cash) + Number(cash.value.esum);
    if (hasSur.value) {
        sum += Number(cash.value.surrendered.amount);
    }
    return sum - cash.value.isum;
});

const expense = computed(() => {
    if (hasSur.value) {
        return Number(cash.value.surrendered.amount) + Number(cash.value.esum);
    }
    return cash.value.esum;
});

const title = computed(() => (store.isToday ? "Hoy" : dformat(store.date, "dddd DD MMM")));

function handleGoList(where = "incomes") {
    const formated = `${store.date}T00:00:00-05:00`;
    const date = new Date(formated);
    date.setDate(date.getDate() + 1);
    store.setRange({
        from: store.date,
        to: iso(date),
    });
    router.push({ name: where });
}

async function fetchData() {
    const { user_code } = useCasherStore();
    const { data } = await fetchCashApi(user_code, store.date);
    cash.value = data.value;
    if (cash.value === null && !store.isToday) {
        warningSnack("No ha sido aperturado");
    }
}

async function refreshAll() {
    const { refreshCasher } = useCasherStore();
    await refreshCasher();
    fetchData();
}

async function manageCash() {
    if (cash.value !== null) {
        await toggleCashApi(cash.value.code);
        fetchData();
    } else {
        hideModal("reauth");
        showModal("new_cash");
    }
}

async function handleOpenCash(code, amount) {
    const { data } = await openCashApi({ code, cash: amount });
    successSnack(data.message);
    hideModal("new_cash");
    fetchData();
}

function openReAuth() {
    const { isMe } = useCasherStore();
    if (!isMe()) {
        warningSnack("No puedes gestionar la caja de otro usuario");
        return;
    }
    let title = "Aperturar caja";
    if (cash.value !== null) {
        if (cash.value.state) title = "Deshabilitar o finalizar caja";
        else title = "Habilitar caja";
    }
    const { showRefresh } = useRefreshStore();
    showRefresh(title, "/check");
}

onMounted(async () => {
    emitter.on("reauthed", manageCash);
    const { cashers, refreshCasher } = useCasherStore();
    if (!cashers.length) {
        await refreshCasher();
    }
    fetchData();
});

onUnmounted(() => {
    emitter.off("reauthed", manageCash);
});
</script>

<template>
    <section id="cash">
        <panel :title="'Caja de ' + title">
            <div class="d-flex space align-items-center overflow-scroll">
                <Datepick @fetch="refreshAll" />
                <span
                    v-if="store.isToday"
                    class="pointer"
                    @click="openReAuth"
                >
                    <i :class="cash_icon" />
                </span>
                <div class="ms-auto">
                    <Casher @changed="fetchData" />
                </div>
            </div>
        </panel>
        <Transition
            name="fade"
            mode="out-in"
        >
            <template v-if="!cash_closed">
                <div class="row mt-4">
                    <div class="col-md-6">
                        <card title="Saldo">
                            <div class="d-flex flex-column">
                                <h1>
                                    <i class="icon ion-ios-wallet text-success" />
                                    {{ $currency(cash.cash) }}
                                </h1>
                                <alert
                                    type="alert-success"
                                    class="my-3"
                                >
                                    <i class="fw-medium">Actualizado por última vez: {{ $ago(cash.updated_at) }}</i>
                                </alert>
                                <m-plain
                                    label="Código"
                                    :value="cash.code"
                                />
                                <m-plain
                                    label="Saldo Inicial"
                                    :value="$currency(initial)"
                                />
                                <m-plain
                                    label="Total Acumulado"
                                    :value="$currency(initial + Number(cash.isum))"
                                />
                                <m-plain
                                    label="Total Egreso"
                                    :value="$currency(expense)"
                                />
                                <hr>
                                <template v-if="hasSur">
                                    <m-plain
                                        label="Monto Rendido"
                                        :value="$currency(cash.surrendered.amount)"
                                    />
                                    <m-plain
                                        label="Responsable de recepción"
                                        :value="cash.surrendered.recipient"
                                    />
                                    <m-plain
                                        label="Medio"
                                        :value="cash.surrendered.reason"
                                    />
                                </template>
                                <p
                                    v-else
                                    class="text-center opacity-50"
                                >
                                    No ha sido rendido
                                </p>
                            </div>
                            <template #foot>
                                <div class="space">
                                    <m-button
                                        data-bs-toggle="modal"
                                        data-bs-target="#surrenderM"
                                        :disabled="hasSur || !store.isToday"
                                        size="btn-sm"
                                        color="btn-inverse-primary"
                                    >
                                        Rendir
                                    </m-button>
                                    <m-router
                                        size="btn-sm"
                                        :to="{ name: 'cashes' }"
                                    >
                                        Reporte Mensual
                                    </m-router>
                                </div>
                            </template>
                        </card>
                    </div>
                    <div class="col-md-6 mt-5 mt-md-0">
                        <div class="d-flex flex-column">
                            <Widget
                                title="Ingresos"
                                icon="icon ion-ios-trending-up text-success"
                                :content="cash.isum"
                                goo-add="invoice"
                                where="incomes"
                                :count="cash.incomes_count"
                                @golist="handleGoList('incomes')"
                            />
                            <Widget
                                class="mt-4"
                                title="Gastos"
                                icon="icon ion-ios-trending-down text-danger"
                                :content="cash.esum"
                                goo-add="new_expense"
                                :count="cash.expenses_count"
                                @golist="handleGoList('expense')"
                            />
                        </div>
                    </div>
                    <Surrender
                        :cash="cash"
                        :fetch-data="fetchData"
                    />
                </div>
            </template>
        </Transition>
        <NewCash
            :last-cash="lastCash"
            @ok="handleOpenCash"
        />
    </section>
</template>

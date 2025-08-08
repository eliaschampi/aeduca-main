<script setup>
import { computed, ref } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
import { fetchAllCyApi, fetchLatestRegApi } from "../../../http";
import { useModalStore } from "../../../pinia/modal";
const emit = defineEmits(["mchangeb"]);
const { showModal, hideModal } = useModalStore();
const props = defineProps({
    branches: {
        type: Array,
        default: () => [],
    },
    dni: String,
});

const cycles = ref([]);
const new_branch = ref(null);
const new_cicle = ref(null);
const current_reg = ref(null);
const ok = ref(false);

const reg = computed(() => {
    if (!current_reg.value) return {};
    const { section_code: sc, state: st } = current_reg.value;
    return {
        isactive: /(a|i|p)/.test(st),
        section: sc,
        full: `${sc.substring(8, 10)} de ${current_reg.value.level}`,
        state: { a: "Activo", p: "Pendiente", i: "Inactivo", f: "Finalizó" }[st],
    };
});

const full_name = (item) => {
    if (item.full_name) {
        return `${item.full_name} ${item.title}`;
    }
    return "Ciclo no disponible";
};

const disableokbtn = computed(() => {
    return !reg.value.isactive ? !new_branch.value : !new_cicle.value;
});

async function fetchLatestReg() {
    const { data } = await fetchLatestRegApi(props.dni);
    current_reg.value = data.value;
}

function handleShowModal(mode = "show") {
    new_branch.value = null;
    new_cicle.value = null;
    ok.value = false;
    if (mode === "show") {
        showModal("obm");
    } else {
        hideModal("obm");
    }
    fetchLatestReg();
}

async function fetchCycles(value) {
    if (!reg.value.isactive) return;
    const { data } = await fetchAllCyApi(value);
    cycles.value = data.values;
}

function handleSubmit() {
    emit("mchangeb", {
        branch_code: new_branch.value,
        cycle_code: new_cicle.value,
        section_code: reg.value.section,
    });
}

defineExpose({
    handleShowModal,
});
</script>
<template>
    <Dialog
        id="obm"
        title="Cambiar de sede"
        btn-name="Enviar"
        :disabled="!ok"
        @ok="handleSubmit"
    >
        <div class="space-b-1">
            <m-plain
                v-if="current_reg"
                label="Última mat:"
                :value="`${reg.full} - ${reg.state}`"
            />
            <m-select
                v-model="new_branch"
                name="new_branch"
                label="Nueva Sede"
                :options="branches"
                standalone
                @update:model-value="fetchCycles"
            />
            <template v-if="reg.isactive">
                <m-select
                    v-model="new_cicle"
                    name="new_cicle"
                    label="Nuevo Nivel o ciclo"
                    :options="cycles"
                    standalone
                    :opt-label="full_name"
                />
                <alert type="alert-warning">
                    <b>Importante </b>
                    La sede destino también debe haber aperturado
                    <b>
                        {{ reg.section.substr(-2) }}
                    </b>
                </alert>
            </template>
            <m-check
                id="gjioer"
                v-model="ok"
                :dis="disableokbtn"
                text="Confirmo que me he comunicado con la sede destino u origen para realizar este procedimiento."
            />
        </div>
    </Dialog>
</template>

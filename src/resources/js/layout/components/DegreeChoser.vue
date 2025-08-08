<script setup>
import { computed, ref, watch } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import { useCycleStore } from "../../pinia/cycle";
import { useDegreeStore } from "../../pinia/degree";
import { useModalStore } from "../../pinia/modal";
const { fetchDegree, fetchDegrees } = useDegreeStore();
const { hideModal } = useModalStore();
const degreeStore = useDegreeStore();
const cycleStore = useCycleStore();
const emit = defineEmits(["changed"]);
const code = ref("");

const disabledDialog = computed(() => {
    return !code.value || code.value === useDegreeStore().code;
});

const cycleOptions = computed(() => {
    return cycleStore.show_overdues ? cycleStore.overdues : cycleStore.actives;
});

async function handleOkClick() {
    hideModal("degreeModal");
    await fetchDegree(code.value);
    emit("changed");
}

function getCyLabel(item) {
    return `${item.full_name} - ${item.title}`;
}

watch(
    () => cycleStore.show_overdues,
    () => {
        degreeStore.$patch({
            degrees: [],
        });
        cycleStore.setCurrent(null);
    },
);
</script>
<template>
    <Dialog
        id="degreeModal"
        title="Elegir un grado"
        :disabled="disabledDialog"
        btn-name="Cambiar"
        @ok="handleOkClick"
    >
        <div class="form-check">
            <input
                id="fwgwa"
                v-model="cycleStore.show_overdues"
                class="form-check-input"
                type="checkbox"
            >
            <label
                class="pointer user-select-none"
                for="fwgwa"
            > Mostrar ciclos anteriores </label>
        </div>
        <div class="row gx-2 mt-4">
            <m-select
                v-model="cycleStore.current"
                name="current"
                standalone
                class="col-md-6"
                label="Ciclo Acádemico"
                :opt-label="getCyLabel"
                :options="cycleOptions"
                @update:model-value="fetchDegrees"
            />
            <m-select
                v-model="code"
                name="code"
                standalone
                class="col-md-6"
                label="Grado"
                opt-label="full_name"
                :options="degreeStore.degrees"
            />
        </div>
    </Dialog>
</template>

<script setup>
import { ref } from "vue";
import Finder from "../../Components/Views/Finder.vue";
import relationTypes from "../../data/relationTypes.json";
import { addStudentFmApi } from "../../http";
import { useBranchStore } from "../../pinia/branch";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
import Dialog from "../Ui/Dialog.vue";
const { hideModal } = useModalStore();
const { successSnack } = useSnackbarStore();

const props = defineProps({
    title: String,
    who: String,
    whoR: String,
    dni: String,
});

const emit = defineEmits(["added"]);

const selected = ref(null);
const relation = ref({
    relation_type: "",
    is_main: false,
});
function handleSelectPerson(person) {
    selected.value = person;
}

function handleModalOpened() {
    const { updateSfmOption } = useBranchStore();
    updateSfmOption({
        who: props.who,
        mode: 2,
        onlyCurrentReg: false,
        onlyCurrentBranch: false,
        after: () => {},
    });
}
async function handleOkClick() {
    if (relation.value.relation_type) {
        const { family_dni, student_dni } =
            props.who === "student"
                ? { family_dni: props.dni, student_dni: selected.value.dni }
                : { family_dni: selected.value.dni, student_dni: props.dni };

        const payload = {
            family_dni,
            student_dni,
            ...relation.value,
        };

        const { data } = await addStudentFmApi(payload);
        hideModal("addRelation");
        successSnack(data.message);
        emit("added");
    }
}
</script>
<template>
    <Dialog
        id="addRelation"
        btn-name="Agregar"
        :title="`Agregar ${props.title}`"
        @open="handleModalOpened"
        @ok="handleOkClick"
    >
        <div class="space-b-1">
            <Finder @input="handleSelectPerson" />
            <template v-if="selected">
                <m-select
                    v-model="relation.relation_type"
                    name="relation_type"
                    :options="relationTypes"
                    label="¿Que parentezco tiene el apoderado?"
                />
                <m-check
                    id="isMain"
                    v-model="relation.is_main"
                    text="El apoderado es encargado Principal"
                />
            </template>
            <template v-else>
                <p class="fst-italic text-center text-capitalize mt-2">
                    {{ props.title }} no seleccionado
                    <RouterLink
                        :to="{ name: props.whoR }"
                        target="_blank"
                    >
                        Registrar Nuevo
                    </RouterLink>
                </p>
            </template>
        </div>
    </Dialog>
</template>

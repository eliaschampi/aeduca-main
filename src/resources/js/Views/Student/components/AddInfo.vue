<script setup>
import { computed, reactive, ref } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
import File from "../../../Components/Ui/File.vue";
import { useModalStore } from "../../../pinia/modal";
import { useRegisterStore } from "../../../pinia/register";
const { showModal, hideModal } = useModalStore();
const emit = defineEmits(["save", "downloadA"]);
const attached = ref(null);
const message = ref("");
const isNew = ref(false);
const newInfo = reactive({});

function handleShowModal(
    payload = {
        type: "Materiales entregados",
        info: "",
        attached: "",
    },
) {
    attached.value = null;
    isNew.value = !payload.student_dni;
    newInfo.code = undefined;
    Object.assign(newInfo, payload);
    showModal("newInfo");
}

function handleUpdateType(payload) {
    if (payload !== "Documentos adjuntos") {
        newInfo.info = "";
    }
}

function handleSaveClick() {
    message.value = "";
    if (!newInfo.info) {
        message.value = "Falta especificar el campo nombre";
        return;
    }
    if (newInfo.type === "Documentos adjuntos") {
        if (isNew.value && !attached.value) {
            message.value = "Archivo adjunto es obligatorio";
            return;
        }
    }
    emit("save", newInfo, attached.value, () => {
        hideModal("newInfo");
    });
}

const mats = computed(() => {
    const { register } = useRegisterStore();
    if (register) {
        const sec = register.section_code.replace(/(\d+)(\w{3})\w*(\w{2})/, "$3/$2 $1");
        return [`Carnet Aeduca (${sec})`, `Libros (${sec})`, "Otro"];
    }
    return ["Carnet educativo", "Libros", "Otro"];
});

defineExpose({
    handleShowModal,
});
</script>
<template>
    <Dialog
        id="newInfo"
        :title="`${isNew ? 'Agregar' : 'Modificar'} un nuevo registro`"
        :btn-name="`${isNew ? 'Agregar' : 'Modificar'}`"
        @ok="handleSaveClick"
    >
        <div class="space-b-1">
            <m-select
                v-model="newInfo.type"
                name="type"
                label="Tipo de información"
                :options="['Materiales entregados', 'Documentos adjuntos']"
                standalone
                :disabled="!isNew"
                opt-label="all"
                opt-value="all"
                @update:model-value="handleUpdateType"
            />
            <template v-if="newInfo.type === 'Documentos adjuntos'">
                <m-input
                    v-model="newInfo.info"
                    name="info"
                    label="Nombre o descripción"
                />
                <template v-if="isNew">
                    <div class="d-flex justify-content-center">
                        <File v-model="attached" />
                    </div>
                </template>
                <p
                    v-else-if="newInfo.attached"
                    class="text-secondary fw-bold pointer"
                    @click="emit('downloadA', newInfo)"
                >
                    <i class="icon ion-md-cloud-download icon-sm text-primary" />
                    Hay un archivo adjunto aquí.
                </p>
            </template>
            <template v-else>
                <m-select
                    v-model="newInfo.info"
                    name="type"
                    label="Nombre"
                    :options="mats"
                    standalone
                    opt-label="all"
                    opt-value="all"
                />
                <m-input
                    v-model="newInfo.attached"
                    name="bookname"
                    standalone
                    label="Observación"
                />
            </template>
            <alert
                v-show="message"
                :dismisable="false"
            >
                {{ message }}
            </alert>
        </div>
    </Dialog>
</template>

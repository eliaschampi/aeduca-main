<script setup>
import { useForm } from "vee-validate";
import { ref } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
import { useModalStore } from "../../../pinia/modal";
const { showModal, hideModal } = useModalStore();
const emit = defineEmits(["udni"]);

const props = defineProps({
    dni: String,
});

const lastdni = ref("");
const newdni = ref("");
const ok = ref(false);
const message = ref("");

const { handleSubmit } = useForm({
    validationSchema: {
        lastdni: {
            required: true,
            numeric: true,
            len: 8,
        },
        newdni: {
            required: true,
            numeric: true,
            len: 8,
        },
    },
});

function handleShowModal(mode = "show") {
    lastdni.value = "";
    newdni.value = "";
    ok.value = false;
    if (mode === "show") {
        showModal("udnim");
    } else {
        hideModal("udnim");
    }
}

const handleSubmitClick = handleSubmit(() => {
    message.value = "";
    if (lastdni.value !== props.dni) {
        message.value = "DNI anterior es incorrecto";
        return;
    }

    if (props.dni === newdni.value) {
        message.value = "DNI nuevo debe ser diferente a la anterior";
        return;
    }

    emit("udni", newdni.value, (msg) => {
        message.value = msg;
    });
});

defineExpose({ handleShowModal });
</script>
<template>
    <Dialog
        id="udnim"
        title="Actualizar DNI"
        btn-name="Enviar"
        :disabled="!ok"
        @ok="handleSubmitClick"
    >
        <div class="space-b-1">
            <m-input v-model="lastdni" name="lastdni" label="DNI anterior" placeholder="DNI" />
            <m-input v-model="newdni" name="newdni" label="Nuevo DNI" placeholder="DNI" />
            <m-check
                id="dwefdwe"
                v-model="ok"
                :dis="!newdni"
                text="Confirmo que el DNI ha sido verificado y validado previamente"
            />
            <alert v-show="message" type="alert-danger">
                {{ message }}
            </alert>
        </div>
    </Dialog>
</template>

<script setup>
import { onMounted, onUnmounted } from "vue";
const emit = defineEmits(["cancel", "open", "ok"]);

const props = defineProps({
    id: {
        type: String,
        default: "myModal",
    },
    title: {
        type: String,
        default: "Agregar Nuevo",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    btnName: {
        type: String,
        default: "Guardar",
    },
    size: {
        type: String,
        default: "",
    },
});

function handleOkClick() {
    if (props.disabled) return;
    emit("ok");
}

function handleCancel() {
    emit("cancel");
}

function handleOpen() {
    emit("open");
}

onMounted(() => {
    const branchModal = document.getElementById(props.id);
    branchModal.addEventListener("show.bs.modal", handleOpen);
    branchModal.addEventListener("hidden.bs.modal", handleCancel);
});

onUnmounted(() => {
    const branchModal = document.getElementById(props.id);
    if (branchModal) {
        branchModal.removeEventListener("show.bs.modal", handleOpen);
        branchModal.removeEventListener("hidden.bs.modal", handleCancel);
    }
});
</script>
<template>
    <div
        :id="props.id"
        class="modal fade"
        role="dialog"
        aria-hidden="true"
        tabindex="-1"
    >
        <div class="modal-dialog cascading-modal" role="document" :class="props.size">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h4 class="title">
                        <i class="icon ion-md-git-branch icon-md"></i>
                        {{ props.title }}
                    </h4>
                    <button
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        type="button"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
                <slot name="foot">
                    <div class="modal-footer justify-content-center">
                        <m-button
                            :disabled="props.disabled"
                            icon="ion-ios-save"
                            color="btn-primary"
                            @ok="handleOkClick"
                        >
                            {{ props.btnName }}
                        </m-button>
                        <m-button data-bs-dismiss="modal" color="btn-warning"> Cancelar </m-button>
                    </div>
                </slot>
            </div>
        </div>
    </div>
</template>

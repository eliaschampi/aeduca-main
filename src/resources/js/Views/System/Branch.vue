<script setup>
import { useForm } from "vee-validate";
import { onMounted, reactive, ref } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import { fetchAllBranchApi, setBranchApi, updateBranchApi } from "../../http";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
const { limited } = useUserStore();
const { successSnack } = useSnackbarStore();
const { showModal, hideModal } = useModalStore();

const branch = reactive({
    name: "",
    email: "",
    address: "",
    telephone: "",
});

const { resetForm, handleSubmit } = useForm({
    validationSchema: {
        name: {
            required_btw: [2, 100],
        },
        email: {
            required: true,
            email: true,
        },
        address: {
            required_btw: [4, 100],
        },
        telephone: {
            required_btw: [2, 50],
        },
    },
});

const branches = ref([]);

async function fetchAllBranch() {
    const { data } = await fetchAllBranchApi();
    branches.value = data.values;
}
function handleShowModal(item) {
    Object.assign(branch, item);
    showModal("branchModal");
}

async function handleSaveBranch() {
    const store = () => {
        if (branch.code) {
            return updateBranchApi(branch);
        }
        return setBranchApi(branch);
    };
    const { data } = await store();
    hideModal("branchModal");
    successSnack(data.message);
    fetchAllBranch();
}

function handleCancel() {
    resetForm({ errors: {} });
    branch.code = undefined;
}

onMounted(fetchAllBranch);

const onSubmit = handleSubmit(handleSaveBranch);
</script>

<template>
    <section id="branch">
        <m-button
            v-show="!limited"
            data-bs-toggle="modal"
            data-bs-target="#branchModal"
            color="rounded-circle btn-success btn-icon fixed-action-btn"
        >
            <i class="icon ion-ios-add" />
        </m-button>
        <div class="mygrid g18">
            <template
                v-for="item in branches"
                :key="item.code"
            >
                <div class="card">
                    <img
                        class="card-up"
                        :src="`/default/user-info${item.code}.jpg`"
                        alt="Portada"
                    >
                    <div class="card-body position-relative">
                        <div
                            class="position-absolute"
                            style="top: -1rem; right: 1rem"
                        >
                            <m-button
                                color="btn-primary"
                                icon="ion-md-options"
                                class="btn-icon"
                                @ok="handleShowModal(item)"
                            />
                        </div>
                        <h5 class="fw-medium text-title">{{ item.name }}</h5>
                        <m-plain
                            wraped
                            label="Correo de Contacto:"
                            :value="item.email"
                        />
                        <m-plain
                            wraped
                            label="Dirección:"
                            :value="item.address"
                        />
                        <m-plain
                            wraped
                            label="Teléfono:"
                            :value="item.telephone"
                        />
                    </div>
                </div>
            </template>
        </div>
        <Dialog
            id="branchModal"
            title="Sede"
            btn-name="Guardar"
            @ok="onSubmit"
            @cancel="handleCancel"
        >
            <div class="space-b-1">
                <m-input
                    v-model="branch.name"
                    name="name"
                    label="Nombre de la Sede"
                />
                <m-input
                    v-model="branch.email"
                    name="email"
                    type="email"
                    label="Correo de Contacto"
                />
                <m-input
                    v-model="branch.address"
                    name="address"
                    label="Direccion"
                />
                <m-input
                    v-model="branch.telephone"
                    name="telephone"
                    label="Telefono de Contacto"
                />
            </div>
        </Dialog>
    </section>
</template>

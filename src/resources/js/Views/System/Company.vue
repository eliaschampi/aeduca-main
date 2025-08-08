<script setup>
import { Form } from "vee-validate";
import { computed, onMounted, reactive } from "vue";
import { fetchCompanyApi, updateConfigApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
const { showSnack, successSnack } = useSnackbarStore();
const { can } = useUserStore();

const company = reactive({});

const isAdmin = computed(() => can("A"));

function handleSubmitClick() {
    showSnack({
        text: "La información sera actualizada",
        button: "Confirmar",
        action: async () => {
            const { data } = await updateConfigApi(company, "company");
            successSnack(data.message);
        },
    });
}

async function fetchData() {
    const { data } = await fetchCompanyApi();
    Object.assign(company, data.values);
}

onMounted(() => {
    fetchData();
});

const schema = {
    name: {
        required: true,
        max: 30,
    },
    main_address: {
        required: true,
        max: 100,
    },
    main_email: {
        required: true,
        email: true,
        max: 30,
    },
    main_phone: {
        required: true,
        phone: true,
        max: 9,
    },
    facebook: {
        required: true,
        max: 100,
    },
    whatsapp_contact: {
        required: true,
        phone: true,
        max: 9,
    },
};
</script>

<template>
    <Form
        :validation-schema="schema"
        @submit="handleSubmitClick"
    >
        <panel
            title="Información de la Institución"
            :f="true"
        >
            <div class="space-b-1">
                <m-input
                    v-model="company.name"
                    :disabled="!isAdmin"
                    name="name"
                    label="Institución"
                />
                <m-input
                    v-model="company.main_address"
                    :disabled="!isAdmin"
                    name="main_address"
                    label="Dirección principal"
                />
                <m-input
                    v-model="company.main_email"
                    :disabled="!isAdmin"
                    name="main_email"
                    label="Correo principal"
                />
                <m-input
                    v-model="company.main_phone"
                    :disabled="!isAdmin"
                    name="main_phone"
                    label="Telf. principal"
                />
                <m-input
                    v-model="company.facebook"
                    :disabled="!isAdmin"
                    name="facebook"
                    label="Facebook"
                />
                <m-input
                    v-model="company.whatsapp_contact"
                    :disabled="!isAdmin"
                    name="whatsapp_contact"
                    label="Whatsapp"
                />
            </div>
            <template #foot>
                <footext>
                    <m-submit :disabled="!isAdmin">Actualizar </m-submit>
                </footext>
            </template>
        </panel>
    </Form>
</template>

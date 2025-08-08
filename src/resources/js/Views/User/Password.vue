<script setup>
import { Form } from "vee-validate";
import { computed, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { changePasswordUserApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
const { showSnack, successSnack } = useSnackbarStore();
const schema = computed(() => ({
    current_password: {
        required: true,
    },
    password: {
        password: true,
    },
    password_confirmation: {
        required: true,
        confirmed: pass.password,
    },
}));

const { push: redirect } = useRouter();
const pass = reactive({ current_password: "", password: "", password_confirmation: "" });

function savePassword() {
    showSnack({
        text: "¿Estas seguro de cambiar tu contraseña?",
        button: "Confirmar",
        action: async () => {
            const { user } = useUserStore();
            const { data } = await changePasswordUserApi({
                ...pass,
                user_code: user.code,
            });
            successSnack(data.message);
            redirect({ name: "user_profile" });
        },
    });
}
const showPass = ref(false);
</script>
<template>
    <Form
        v-slot="{ meta }"
        autocomplete="off"
        :validation-schema="schema"
        @submit="savePassword"
    >
        <card title="Seguridad">
            <template #rb>
                <m-action
                    tool="Mostrar contraseña"
                    :icon="showPass ? 'eye' : 'eye-off'"
                    color="primary-emphasis"
                    @action="showPass = !showPass"
                />
            </template>
            <div class="space-b-1">
                <m-input
                    v-model="pass.current_password"
                    name="current_password"
                    label="Contraseña Actual"
                    placeholder="********"
                    :type="showPass ? 'text' : 'password'"
                />
                <m-input
                    v-model="pass.password"
                    name="password"
                    label="Nueva Contraseña"
                    placeholder="********"
                    :type="showPass ? 'text' : 'password'"
                />
                <m-input
                    v-model="pass.password_confirmation"
                    name="password_confirmation"
                    label="Repetir la Contraseña"
                    placeholder="********"
                    :type="showPass ? 'text' : 'password'"
                />
            </div>
            <template #foot>
                <footext>
                    <m-submit :disabled="!meta.valid">Actualizar</m-submit>
                </footext>
            </template>
        </card>
    </Form>
</template>

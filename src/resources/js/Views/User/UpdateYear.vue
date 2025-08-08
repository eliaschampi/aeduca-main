<script setup>
import { computed, onMounted, ref } from "vue";
import { changeCurrentYearUserApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";

const years = ref([]);
const currentYear = ref(new Date().getFullYear());
const { fullyear } = useUserStore();
const { showSnack, successSnack } = useSnackbarStore();
const disableYearBtn = computed(() => parseInt(currentYear.value) === fullyear);

onMounted(() => {
    const lastYear = 2019;
    for (let year = lastYear; year <= currentYear.value; year++) {
        years.value.push(year);
    }
    currentYear.value = fullyear;
});

async function handleUpdateYear() {
    showSnack({
        text: "Se modificará el año académico",
        button: "confirmar",
        action: async () => {
            const currentYearValue = parseInt(currentYear.value);
            const { data } = await changeCurrentYearUserApi(currentYearValue);
            const { updateUserCachedProps } = useUserStore();
            updateUserCachedProps({
                current_year: currentYearValue,
            });
            successSnack(data.message);
            location.reload();
        },
    });
}
</script>
<template>
    <Card title="Opciones avanzadas">
        <div class="space-b-1">
            <Alert type="alert-secondary">
                La información académica y administrativa del sistema será gestionado solamente del
                año establecido
            </Alert>

            <MSelect
                v-model="currentYear"
                name="year"
                label="Año"
                standalone
                :options="years"
            />
        </div>
        <template #foot>
            <MButton :disabled="disableYearBtn" color="btn-success" @ok="handleUpdateYear">
                Guardar
            </MButton>
        </template>
    </Card>
</template>
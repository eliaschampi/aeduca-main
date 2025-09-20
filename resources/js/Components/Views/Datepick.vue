<script setup>
import { iso } from "../../core/date";
import { useDateStore } from "../../pinia/date";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";

const { warningSnack } = useSnackbarStore();
const { fullyear } = useUserStore();
const store = useDateStore();
const emit = defineEmits(["fetch"]);

const isValidDate = () => {
    let today = new Date();
    if (fullyear < today.getFullYear()) {
        today = `${fullyear}-12-31`;
    } else {
        today.setDate(today.getDate() + 1);
        today = iso(today);
    }
    return store.date && store.date >= `${fullyear}-01-01` && store.date <= today;
};

const handleSubmit = () => {
    const valid = isValidDate();
    valid ? emit("fetch") : warningSnack("Fecha no válida");
};
</script>
<template>
    <form style="max-width: 18rem" @submit.prevent="handleSubmit">
        <div class="input-group">
            <input
                id="iddatepick"
                v-model="store.date"
                class="form-control"
                name="fecha"
                type="date"
            />
            <m-submit class="input-group-text" color="btn-success" icon="icon ion-md-search" />
        </div>
    </form>
</template>

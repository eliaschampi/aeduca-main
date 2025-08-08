<script setup>
import { computed } from "vue";
import { useSnackbarStore } from "../../pinia/snackbar";

const snackbar = useSnackbarStore();

const active = computed(() => snackbar.active);
const text = computed(() => snackbar.text);
const theme = computed(() => snackbar.theme);
const button = computed(() => snackbar.button);
const action = computed(() => snackbar.action);

const icon = "icon ion-ios-notifications icon-md";

const closeSnackbar = () => {
    snackbar.closeSnack();
};

const buttonClick = async () => {
    if (typeof action.value === "function") {
        await action.value();
    }
};

const styles = computed(() => {
    const clases = ["snackbar", "space-1", "snackbar-" + theme.value];
    const activeClass = active.value ? "active" : "";
    return clases.concat(activeClass);
});
</script>
<template>
    <div
        id="snackbar"
        :class="styles.join(' ')"
    >
        <div
            class="snackbar_icon pointer"
            :mytooltip="text"
        >
            <span :class="icon" />
        </div>
        <div class="snackbar__text">
            {{ text }}
        </div>
        <template v-if="button">
            <div
                class="badge bg-warning-subtle text-warning-emphasis border border-warning border-1 pointer"
                @click.prevent="buttonClick"
            >
                {{ button }}
            </div>
        </template>
        <div
            class="times pointer"
            @click="closeSnackbar"
        >
            <span class="icon ion-md-close icon-md" />
        </div>
    </div>
</template>

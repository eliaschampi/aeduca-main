<script setup>
import { computed } from "vue";

const props = defineProps({
    size: {
        type: String,
        default: "4.5em",
    },
});

const styles = computed(() => {
    return {
        width: props.size,
        height: props.size,
    };
});
</script>
<template>
    <div class="text-center">
        <svg
            class="spinner spinner--circle"
            :style="styles"
            viewBox="0 0 66 66"
            xmlns="http://www.w3.org/2000/svg"
        >
            <circle
                class="path"
                cx="33"
                cy="33"
                fill="none"
                r="30"
                stroke-linecap="round"
                stroke-width="6"
            />
        </svg>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    color: {
        type: String,
        default: "#ff304f",
    },
    size: {
        type: String,
        default: "15px",
    },
    margin: {
        type: String,
        default: "2px",
    },
    radius: {
        type: String,
        default: "100%",
    },
});

// define spinnerStyle object in vue computed
const spinnerStyle = computed(() => {
    return {
        width: props.size,
        height: props.size,
        margin: props.margin,
        borderRadius: props.radius,
        backgroundColor: props.color,
        display: "inline-block",
        animationName: "v-pulseStretchDelay",
        animationDuration: "0.75s",
        animationIterationCount: "infinite",
        animationTimingFunction: "cubic-bezier(.2,.68,.18,1.08)",
        animationFillMode: "both",
    };
});

const spinnerDelay1 = computed(() => ({ animationDelay: "0.12s" }));

const spinnerDelay2 = computed(() => ({ animationDelay: "0.24s" }));

const spinnerDelay3 = computed(() => ({ animationDelay: "0.36s" }));
</script>
<template>
    <div class="v-spinner mt-3">
        <div class="v-pulse v-pulse1" :style="[spinnerStyle, spinnerDelay1]"></div>
        <div class="v-pulse v-pulse2" :style="[spinnerStyle, spinnerDelay2]"></div>
        <div class="v-pulse v-pulse3" :style="[spinnerStyle, spinnerDelay3]"></div>
    </div>
</template>

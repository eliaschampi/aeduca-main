<script setup>
const props = defineProps({
    color: {
        type: String,
        default: "btn-info",
    },
    size: {
        type: String,
        default: "btn-md",
    },
    icon: {
        type: String,
        default: "",
    },
    to: [Object, String],
});
</script>
<template>
    <RouterLink
        :class="['btn', props.color, props.size]"
        :to="props.to"
    >
        <i
            v-show="props.icon"
            :class="props.icon"
        />
        <slot />
    </RouterLink>
</template>

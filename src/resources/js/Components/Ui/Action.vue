<script setup>
defineProps({
    icon: {
        type: String,
        default: "create",
    },
    color: {
        type: String,
        default: "success",
    },
    tool: {
        type: String,
        default: "Modificar",
    },
});
const emit = defineEmits(["action"]);
</script>
<template>
    <span
        :class="'pointer text-' + color"
        :mytooltip="tool"
        @click="emit('action')"
    >
        <i :class="'icon icon-md ion-md-' + icon" />
    </span>
</template>

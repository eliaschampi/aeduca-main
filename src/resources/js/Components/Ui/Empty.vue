<script setup>
const props = defineProps({
    title: {
        type: String,
        default: "No hay nada para mostrar",
    },
});
</script>
<template>
    <div class="text-center my-4">
        <img
            src="/img/empty.svg"
            style="max-width: 200px"
        >
        <i class="fw-medium opacity-50 d-block mt-2">{{ props.title }}</i>
    </div>
</template>

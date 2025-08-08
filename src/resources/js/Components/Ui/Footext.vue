<script setup>
defineProps({
    title: {
        type: String,
    },
});
</script>
<template>
    <div class="d-flex flex-column align-items-center space-b-1">
        <p v-show="title" class="opacity-75">
            {{ title }}
        </p>
        <slot></slot>
    </div>
</template>

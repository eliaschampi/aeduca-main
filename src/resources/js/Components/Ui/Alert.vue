<script setup>
const props = defineProps({
    type: {
        type: String,
        default: "alert-info",
    },
    dismisable: {
        type: Boolean,
        default: true,
    },
    title: {
        type: String,
        default: "",
    },
});
</script>
<template>
    <div class="alert alert-dismissible fade show" :class="props.type">
        <h4 v-show="props.title" class="alert-heading">
            {{ props.title }}
        </h4>
        <button
            v-if="props.dismisable"
            class="btn-close"
            data-bs-dismiss="alert"
            type="button"
        ></button>
        <slot></slot>
    </div>
</template>

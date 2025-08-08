<script setup>
defineProps({
    color: {
        type: String,
        default: "btn-inverse-success",
    },
    size: {
        type: String,
        default: "btn-md",
    },
    icon: {
        type: String,
        default: "icon ion-ios-save",
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>
<template>
    <button
        type="submit"
        :class="['btn', color, size]"
        :disabled="isLoading || disabled"
    >
        <template v-if="isLoading"> <i class="icon ion-md-globe infinite-spin" /> </template>
        <template v-else>
            <i
                v-show="icon || !isLoading"
                :class="icon"
            />
            <slot />
        </template>
    </button>
</template>

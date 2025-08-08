<script setup>
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "",
    },
    f: {
        type: Boolean,
        default: false,
    },
});
</script>
<template>
    <div
        class="card"
        :class="props.type"
    >
        <div class="card-body">
            <div
                v-if="props.title"
                class="card-title"
            >
                {{ props.title }}
            </div>
            <slot />
        </div>
        <div
            v-if="props.f"
            class="card-footer"
        >
            <slot name="foot" />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    wraped: {
        type: Boolean,
    },
    label: {
        type: String,
    },
    value: {
        type: [String, Number],
    },
});
</script>
<template>
    <div
        class="row"
        :class="props.wraped ? 'my-1' : 'space-b'"
    >
        <div
            class="col-sm-12 fw-medium"
            :class="{ 'col-md-3': !props.wraped }"
        >
            {{ props.label }}
        </div>
        <div
            class="col-sm-12"
            :class="{ 'col-md-9': !props.wraped }"
        >
            <slot>{{ props.value }}</slot>
        </div>
    </div>
</template>

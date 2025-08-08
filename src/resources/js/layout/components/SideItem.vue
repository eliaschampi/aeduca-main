<script setup>
const props = defineProps({
    label: String,
    to: Object,
    visible: {
        type: Boolean,
        default: true,
    },
});
</script>
<template>
    <RouterLink
        v-slot="{ href, navigate, isActive }"
        custom
        :to="props.to"
    >
        <li
            v-show="props.visible"
            class="nav-item"
            :class="{ active: isActive }"
        >
            <a
                class="nav-link"
                :href="href"
                @click="navigate"
            >
                {{ props.label }}
            </a>
        </li>
    </RouterLink>
</template>

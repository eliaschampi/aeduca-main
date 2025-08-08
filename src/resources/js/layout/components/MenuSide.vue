<script setup>
const props = defineProps({
    id: String,
    icon: String,
    name: String,
});
</script>
<template>
    <li class="nav-item">
        <a
            class="nav-link"
            data-bs-toggle="collapse"
            aria-expanded="false"
            :href="`#${props.id}`"
            :aria-controls="props.id"
        >
            <i
                class="menu-icon"
                :class="props.icon"
            />
            <span class="menu-title">{{ props.name }}</span>
            <i class="menu-arrow" />
        </a>
        <div
            :id="props.id"
            class="collapse"
        >
            <ul class="nav flex-column sub-menu">
                <slot />
            </ul>
        </div>
    </li>
</template>

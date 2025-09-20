<script setup>
defineProps({
    image: String,
    dni: String,
    route: String,
    name: String,
    lastname: String,
});
</script>
<template>
    <div class="card card-body">
        <div class="d-flex align-items-start space">
            <slot name="icon">
                <div class="avatar pointer">
                    <img
                        width="40"
                        height="40"
                        class="bg-info"
                        :style="{
                            borderRadius: '50%',
                            boxShadow: `var(--shadow)`,
                        }"
                        :src="`/default/${image || 'avatar.png'}`"
                    />
                </div>
            </slot>
            <div>
                <RouterLink
                    class="fw-medium"
                    :to="{
                        name: route,
                        params: { dni },
                    }"
                >
                    {{ `${name} ${lastname}` }}
                </RouterLink>
                <slot></slot>
            </div>
            <div class="ms-auto">
                <slot name="right"></slot>
            </div>
        </div>
    </div>
</template>

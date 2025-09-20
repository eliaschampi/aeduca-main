<script setup>
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";
const { go: goBack } = useRouter();
const route = useRoute();
const title = computed(() => route.meta.title);
</script>
<template>
    <ol class="mb-4 d-flex align-items-center" style="width: 100%">
        <div class="d-flex me-2">
            <RouterLink to="/">
                <i class="ion ion-md-home icon-md"></i>
            </RouterLink>
            <a class="mx-2" type="button" @click="goBack(-1)">
                <i class="ion ion-md-arrow-round-back text-primary icon-md"></i>
            </a>
        </div>
        <strong>{{ title }}</strong>
    </ol>
</template>

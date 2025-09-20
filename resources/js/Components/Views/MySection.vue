<script setup>
import { computed, watchEffect } from "vue";
import { useDegreeStore } from "../../pinia/degree";
import { useSectionStore } from "../../pinia/section";
const emit = defineEmits(["done"]);
const degree_code = computed(() => useDegreeStore().degree.code);
const sectionStore = useSectionStore();
const emitDone = () => emit("done");

watchEffect(async () => {
    await sectionStore.fetchSections(degree_code.value);
    emitDone();
});
</script>
<template>
    <m-select
        v-model="sectionStore.section_code"
        name="mysection"
        style="width: 10rem"
        placeholder="Sección"
        standalone
        :options="sectionStore.sections"
        @update:model-value="emitDone"
    />
</template>

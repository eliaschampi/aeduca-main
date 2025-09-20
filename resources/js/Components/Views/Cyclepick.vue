<script setup>
import { onMounted } from "vue";
import { useCycleStore } from "../../pinia/cycle";
const cycleStore = useCycleStore();
onMounted(cycleStore.fetchCycles);
</script>

<template>
    <div class="d-flex align-items-center space flex-wrap">
        <div class="selectgroup selectgroup-pills">
            <template v-for="cy in cycleStore.actives" :key="cy.code">
                <label class="selectgroup-item" :mytooltip="cy.title">
                    <input
                        v-model="cycleStore.current"
                        type="radio"
                        name="mode"
                        class="selectgroup-input"
                        :value="cy.code"
                    />
                    <span class="selectgroup-button">
                        {{ cy.full_name }}
                    </span>
                </label>
            </template>
        </div>
    </div>
</template>

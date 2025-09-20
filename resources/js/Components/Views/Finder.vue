<script setup>
import { computed, inject, onMounted, onUnmounted, ref, watch } from "vue";
import { useEntityStore, usePlaceholder } from "../../composables/utility";
import axios from "../../http/axios.js";
import { useBranchStore } from "../../pinia/branch";
import { useUserStore } from "../../pinia/user";

const emit = defineEmits(["input"]);
const store = useBranchStore();
const name = ref("");
const values = ref([]);
const selected = ref(null);
const isLoading = ref(false);
const only_reg = ref(false);
const branch_code = ref(null);
const whoStored = computed(() => store.sfmOps.who);
const placeholder = computed(() => {
    return usePlaceholder(whoStored.value);
});

const emitter = inject("emitter");

const showOptions = computed(() => {
    return !store.sfmOps.onlyCurrentReg && whoStored.value === "student";
});

function resetMyOptionValues() {
    only_reg.value = true;
    branch_code.value = useUserStore().branch_code;
}

async function search() {
    if (isLoading.value) {
        return;
    }

    const term = name.value.trim();
    if (!term) {
        return;
    }

    isLoading.value = true;

    let route = `/${whoStored.value}/search/${term}`;
    if (whoStored.value === "student") {
        route = `/student/search/${branch_code.value}/${only_reg.value}/${term}`;
    }

    const { data } = await axios.get(route);
    values.value = data.values;
    isLoading.value = false;
}

function handleItemChange(value) {
    emit("input", { ...selected.value, branch_code: value });
}

watch(whoStored, () => {
    values.value = [];
});

onMounted(() => {
    resetMyOptionValues();
    const { getStore } = useEntityStore();
    const { dni, fullname } = getStore(whoStored.value);
    if (!name.value && dni) {
        name.value = fullname;
    }
    emitter.on("sfm_has_closed", resetMyOptionValues);
});

onUnmounted(() => {
    emitter.off("sfm_has_closed", resetMyOptionValues);
});
</script>
<template>
    <form class="d-flex mb-4" @submit.prevent="search">
        <input v-model="name" type="text" :placeholder="placeholder" class="form-control" />
        <div class="d-flex">
            <m-submit
                :is-loading="isLoading"
                :disabled="name.length < 3"
                color="btn-inverse-warning"
                class="btn-icon"
                icon="ion ion-ios-search"
            />
            <div v-show="showOptions" class="dropdown">
                <m-button
                    id="searchEMenu"
                    color="btn-success"
                    icon="ion-md-funnel"
                    class="btn-icon"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                />
                <ul class="dropdown-menu" aria-labelledby="searchEMenu">
                    <template v-for="item in store.branchesForFinder" :key="item.code">
                        <li class="dropdown-item">
                            <div class="form-check" @click.stop>
                                <input
                                    v-model="branch_code"
                                    class="form-check-input"
                                    type="radio"
                                    name="branch_r"
                                    :value="item.code"
                                />
                                <label
                                    class="form-check-label pointer user-select-none"
                                    @click="branch_code = item.code"
                                >
                                    {{ item.name }}
                                </label>
                            </div>
                        </li>
                    </template>
                    <li class="dropdown-item">
                        <div class="form-check" @click.stop>
                            <input
                                id="onlyactive_i"
                                v-model="only_reg"
                                type="checkbox"
                                class="form-check-input"
                                aria-label="onlystudent"
                            />
                            <label class="pointer user-select-none" for="onlyactive_i">
                                Solo
                                <div class="badge bg-success">Activos</div>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </form>
    <div>
        <ul class="list-group list-group-flush">
            <li v-for="item in values" :key="item.dni" class="list-group-item">
                <div class="form-check">
                    <input
                        :id="item.dni"
                        v-model="selected"
                        name="grouped"
                        type="radio"
                        class="form-check-input"
                        :value="item.person"
                        @change="handleItemChange(item.branch_code)"
                    />
                    <label class="form-check-label pointer user-select-none" :for="item.dni">
                        {{ item.person.name + " " + item.person.lastname }}
                        <small class="opacity-50 fst-italic"> ({{ item.dni }}) </small>
                    </label>
                </div>
            </li>
        </ul>
    </div>
</template>

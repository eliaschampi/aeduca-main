<script setup>
import { onMounted, ref } from "vue";
import { fetchTeachersApi, fetchCyclesApi } from "../../http";
import PersonCard from "../../Components/Views/PersonCard.vue";

const state = ref(true);
const specialties = ref([]);
const specialty = ref(null);
const teachers = ref([]);

async function fetchAll() {
    const { data } = await fetchTeachersApi(specialty.value, state.value);
    teachers.value = data.values;
}

async function fetchCycles() {
    const { data } = await fetchCyclesApi();
    specialties.value = data.values;
    specialty.value = Object.keys(data.values)[0];
}

onMounted(async () => {
    await fetchCycles();
    fetchAll();
});
</script>
<template>
    <section>
        <panel title="Registro general de Docentes">
            <div class="d-flex align-items-center space">
                <m-select
                    v-model="specialty"
                    name="specialty"
                    placeholder="Especialidad"
                    :options="specialties"
                    :opt-value="(_, key) => key"
                    @update:model-value="fetchAll"
                />
                <m-check
                    id="wistiki"
                    v-model="state"
                    text="Solo activos"
                    @change="fetchAll"
                />
                <m-router
                    v-can="'AS'"
                    size="btn-sm"
                    :to="{ name: 'new_teacher' }"
                    color="btn-inverse-info"
                >
                    Agregar
                </m-router>
            </div>
        </panel>
        <div class="mygrid g18 mt-4">
            <template
                v-for="item in teachers"
                :key="item.code"
            >
                <PersonCard
                    :dni="item.dni"
                    :name="item.person.name"
                    :lastname="item.person.lastname"
                    :image="item.person.profile?.image"
                    route="teacher_profile"
                >
                    <div class="text-small d-flex align-items-center space">
                        <span class="icon ion-md-call text-success me-1" />
                        <span>{{ item.person.phone }}</span>
                    </div>
                    <template #right>
                        <m-router
                            v-can="'AS'"
                            color="btn-inverse-success"
                            icon="icon ion-md-create"
                            class="btn-icon"
                            :to="{
                                name: 'new_teacher',
                                params: { dni: item.dni },
                            }"
                        />
                    </template>
                </PersonCard>
            </template>
        </div>
    </section>
</template>

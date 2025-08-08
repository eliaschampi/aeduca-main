<script setup>
import { computed, ref, watchEffect } from "vue";
import days from "../../data/weekDays.json";
import { fetchByTeacherShApi, updateShApi } from "../../http";
import { useDegreeStore } from "../../pinia/degree";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useTeacherStore } from "../../pinia/teacher";
const { code: degree_code } = useDegreeStore();
const columns = ["Grado y Sec.", "Curso", "Desde", "Hasta", "Acciones"];
const day = ref(1);
const { successSnack } = useSnackbarStore();
const schedules = ref([]);
const fdays = Object.values(days).slice(1, 7);
const storedDni = computed(() => useTeacherStore().dni);

const filtered = computed(() => {
    return schedules.value.filter((item) => item.day === day.value || day.value === 0);
});

async function fetchData(dni) {
    const { data } = await fetchByTeacherShApi(dni);
    schedules.value = data.values.map((item) => {
        const stv = item.op.sts;
        const st = stv[0];
        let stext = "";
        stext = st.length === 10 ? `${st.substring(8)} de ${st.substring(4, 6)}` : st;
        if (stv.length > 1) {
            stext += " y otros";
        }
        return {
            code: item.code,
            day: item.day,
            from_time: item.from_time,
            to_time: item.to_time,
            course: item.op.course.name,
            stext,
        };
    });
}

watchEffect(() => {
    fetchData(storedDni.value);
});

async function handleDeleteSchedule(item) {
    const { data } = await updateShApi(item);
    successSnack(data.message);
    schedules.value = schedules.value.filter((i) => i.code !== item.code);
}
</script>
<template>
    <section>
        <ul class="nav nav-line nav-fill">
            <li
                v-for="d in fdays"
                :key="d.code"
                class="nav-item pointer"
                @click="day = d.code"
            >
                <div
                    class="nav-link"
                    :class="{ active: day === d.code }"
                >
                    {{ d.name }}
                </div>
            </li>
        </ul>
        <m-table
            :columns="columns"
            :data="filtered"
            :head="false"
        >
            <template #data="{ rows }">
                <tr
                    v-for="item in rows"
                    :key="item.code"
                >
                    <td>{{ item.stext }}</td>
                    <td>{{ item.course }}</td>
                    <td>{{ item.from_time }}</td>
                    <td>{{ item.to_time }}</td>
                    <td
                        v-can="'AS'"
                        class="space"
                    >
                        <m-action
                            icon="trash"
                            color="danger"
                            tool="Eliminar"
                            @action="handleDeleteSchedule(item)"
                        />
                    </td>
                </tr>
            </template>
        </m-table>
        <m-router
            size="btn-sm"
            :to="{
                name: 'schedule',
                params: { degree_code },
            }"
        >
            Horarios por sección
        </m-router>
    </section>
</template>

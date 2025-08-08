<script setup>
import { useForm } from "vee-validate";
import { reactive } from "vue";
import Dialog from "../../Components/Ui/Dialog.vue";
import { useModalStore } from "../../pinia/modal";
const { showModal } = useModalStore();
defineProps({
    days: Object,
    ops: Array,
});

const sched = reactive({});

const emit = defineEmits(["ok"]);

const { resetForm, handleSubmit } = useForm({
    validationSchema: {
        op_code: {
            required: true,
        },
        day: {
            required: true,
        },
        from_time: {
            required: true,
        },
        to_time: {
            required: true,
        },
    },
});

function show({ code, op_code, day, from_time, to_time }) {
    Object.assign(sched, {
        code: code || undefined,
        op_code,
        day,
        from_time: from_time || "08:00:00",
        to_time: to_time || "14:00:00",
    });
    showModal("newSchedule");
}

function handleCancel() {
    resetForm({ errors: {} });
    sched.op_code = null;
    sched.day = null;
}

const onSubmit = handleSubmit(() => {
    emit("ok", sched);
});

defineExpose({
    show,
});
</script>
<template>
    <Dialog
        id="newSchedule"
        :title="!sched.code ? 'Agregar horario' : 'Modificar'"
        btn-name="Guardar"
        @ok="onSubmit"
        @cancel="handleCancel"
    >
        <div class="space-b-1">
            <m-select
                v-show="ops.length"
                v-model="sched.op_code"
                name="op_code"
                label="Curso"
                :options="ops"
                :opt-label="(s) => `${s.course.name}. Docente ${s.person.name}`"
            />
            <m-select
                v-model="sched.day"
                name="day"
                label="Dia"
                :options="days"
            />
            <div class="row gx-2">
                <m-input
                    v-model="sched.from_time"
                    class="col-md-6 mb-3"
                    name="from_time"
                    type="time"
                    label="Desde"
                />
                <m-input
                    v-model="sched.to_time"
                    class="col-md-6 mb-3"
                    name="to_time"
                    type="time"
                    label="Hasta"
                />
            </div>
        </div>
    </Dialog>
</template>

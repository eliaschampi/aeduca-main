<script setup>
import { computed } from "vue";

const props = defineProps({
    code: Number,
    state: {
        type: String,
        default: "",
    },
    time: String,
    hasJus: {
        type: Boolean,
    },
    stateLabel: String,
    mod: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(["update", "showJusModal", "edit", "cancel", "update:state", "update:time"]);

const isAbsence = computed(() => ["justificado", "envió justificación", "falta"].includes(props.state));

const isTimeRequired = computed(() => ["presente", "tarde"].includes(props.state));

const disableSaveModButton = computed(() => !(isTimeRequired.value && !props.time));

const localState = computed({
    get: () => props.state,
    set: (value) => emit("update:state", value),
});
const localEntryTime = computed({
    get: () => props.time,
    set: (value) => emit("update:time", value),
});
</script>
<template>
    <tr>
        <slot />
        <template v-if="mod === props.code">
            <td>
                <input
                    v-show="isTimeRequired"
                    v-model="localEntryTime"
                    style="max-width: 7rem"
                    class="form-control form-control-sm"
                    type="time"
                    step="1"
                >
            </td>
            <td>
                <select
                    v-model="localState"
                    style="max-width: 6rem"
                    class="form-select form-select-sm"
                >
                    <option value="permiso">Permiso</option>
                    <option value="falta">Falta</option>
                    <option value="presente">Presente</option>
                    <option value="tarde">Tarde</option>
                </select>
            </td>
            <td class="space">
                <m-action
                    v-show="disableSaveModButton"
                    color="success"
                    icon="save"
                    @action="emit('update')"
                />
                <m-action
                    icon="close"
                    color="danger"
                    tool="Cancelar"
                    @action="emit('cancel')"
                />
            </td>
        </template>
        <template v-else>
            <td>{{ props.time }}</td>
            <td>
                <span :class="props.stateLabel">{{ props.state }}</span>
            </td>
            <td class="space">
                <m-action
                    v-show="isAbsence"
                    icon="hand"
                    color="primary"
                    tool="Justificación"
                    @action="emit('showJusModal')"
                />
                <m-action
                    v-show="props.hasJus"
                    tool="Editar"
                    @action="emit('edit')"
                />
            </td>
        </template>
    </tr>
</template>

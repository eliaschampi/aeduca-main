<script setup>
import { useRouter } from "vue-router";
import { useUserStore } from "../../pinia/user";

const props = defineProps({
    user: {
        type: Object,
        default: () => {
            return {};
        },
    },
    role: {
        type: String,
        default: "",
    },
    isAdmin: Boolean,
});

const router = useRouter();

const {
    user: { code: user_code },
} = useUserStore();

const emit = defineEmits(["changestate"]);

function profile() {
    router.push({
        name: "user_profile",
        params: { code: props.user.code },
    });
}

function handleEditClick() {
    const { limited } = useUserStore();
    if (limited) return;
    router.push({
        name: "update_user",
        params: { code: props.user.code },
    });
}
</script>

<template>
    <div class="card text-center profile-card user-card">
        <div class="card-up aqua-gradient" />
        <div class="avatar mx-auto">
            <img
                class="rounded-circle img-fluid"
                :src="`/default/${props.user.image}`"
            >
            <m-button
                v-if="props.isAdmin"
                class="rounded-circle btn-icon"
                color="btn-success"
                @ok="handleEditClick"
            >
                <i class="icon ion-md-create" />
            </m-button>
        </div>
        <div class="card-body space-b">
            <h5>
                {{ `${props.user.name} ${props.user.lastname}` }}
            </h5>
            <div
                v-if="user_code !== user.code"
                class="mx-auto switch"
            >
                <input
                    :id="'tg' + user.dni"
                    type="checkbox"
                    :disabled="!props.isAdmin"
                    :checked="props.user.state"
                    @input="
                        emit('changestate', {
                            code: props.user.code,
                            state: $event.target.checked,
                        })
                    "
                >
                <label
                    class="form-label success"
                    :for="'tg' + props.user.dni"
                >Toggle</label>
            </div>
            <template v-else>
                <m-button
                    class="mx-auto rounded-circle btn-icon"
                    @ok="profile"
                >
                    <i class="icon ion-md-person" />
                </m-button>
            </template>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-2">
                {{ props.role }}
            </span>
        </div>
    </div>
</template>

<script setup>
import { Form } from "vee-validate";
const props = defineProps({
    title: String,
    subtitle: String,
    res: {
        type: Object,
        default: () => ({}),
    },
    isLoading: Boolean,
    btn: String,
    schema: Object,
});

const emit = defineEmits(["onsub"]);

function handleSubmit() {
    emit("onsub");
}
</script>
<template>
    <div class="card mt-4 p-2 p-md-3 p-lg-4">
        <div class="card-header">
            <div class="card-title d-flex space text-dark">
                <img
                    src="/img/carrion.svg"
                    alt="logo"
                    width="50"
                    style="max-width: 60px"
                >
                <div class="opacity-50">
                    {{ props.title }}
                    <div class="text-small">
                        {{ props.subtitle }}
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <Form
                autocomplete="off"
                :validation-schema="props.schema"
                @submit="handleSubmit"
            >
                <slot />
                <alert
                    v-show="props.res.message"
                    :type="props.res.alert"
                >
                    {{ props.res.message }}
                </alert>
                <div class="text-center">
                    <m-submit
                        size="btn-lg"
                        :is-loading="props.isLoading"
                        color="btn-info"
                    >
                        {{ props.btn }}
                    </m-submit>
                </div>
            </Form>
        </div>
        <div class="card-footer">
            <slot name="foot" />
        </div>
    </div>
</template>

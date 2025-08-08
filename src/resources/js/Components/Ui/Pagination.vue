<script setup>
import { computed } from "vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({
            current_page: 1,
            from: 1,
            last_page: 1,
            next_page_url: null,
            per_page: 10,
            prev_page_url: null,
            to: 1,
            total: 0,
        }),
    },
    limit: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(["pagination-change-page"]);

const validated = () => computed(() => props.data.total > props.data.per_page);
const selectPage = (page) => {
    if (page === "...") {
        return;
    }
    emit("pagination-change-page", page);
};

const getPages = () => {
    if (props.limit === -1) {
        return 0;
    }
    if (props.limit === 0) {
        return props.data.last_page;
    }

    const current = props.data.current_page;
    const last = props.data.last_page;
    const delta = props.limit;
    const left = current - delta;
    const right = current + delta + 1;
    const range = [];
    const pages = [];

    for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= left && i < right)) {
            range.push(i);
        }
    }

    let l;

    range.forEach((i) => {
        if (l) {
            if (i - l === 2) {
                pages.push(l + 1);
            } else if (i - l !== 1) {
                pages.push("...");
            }
        }
        pages.push(i);
        l = i;
    });

    return pages;
};
</script>
<template>
    <!-- eslint-disable -->
    <ul class="pagination mb-0" v-if="validated">
        <li class="page-item" v-if="props.data.prev_page_url">
            <span class="page-link pointer" aria-label="Previous" @click="selectPage(--props.data.current_page)">
                Anterior
            </span>
        </li>
        <li
            class="page-item"
            v-for="(item, index) in getPages()"
            :class="{ active: item === props.data.current_page }"
            :key="index"
        >
            <a class="page-link" href="#" @click.prevent="selectPage(item)">
                {{ item }}
            </a>
        </li>
        <li class="page-item" v-if="props.data.next_page_url">
            <span class="page-link pointer" aria-label="Next" @click="selectPage(++props.data.current_page)">
                Siguiente
            </span>
        </li>
    </ul>
</template>

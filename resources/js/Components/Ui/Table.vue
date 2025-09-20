<script setup>
import { onMounted, ref } from "vue";
import Empty from "./Empty.vue";
import PulseLoader from "./PulseLoader.vue";

const props = defineProps({
  fetch: {
    type: Function,
    default: () => ({}),
  },
  columns: {
    type: Array,
    default: () => {
      return [];
    },
  },
  data: {
    type: Array,
    default: () => {
      return [];
    },
  },
  head: {
    type: Boolean,
    default: true,
  },
  modelValue: {
    type: String,
    default: "",
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
  emptytext: {
    type: String,
    default: "Aún no hay registros",
  },
});

const emit = defineEmits(["update:modelValue", "refresh"]);

const fetchingData = ref(false);

function handleInput(event) {
  emit("update:modelValue", event.target.value);
}

async function fetchData() {
  fetchingData.value = true;
  await props.fetch();
  fetchingData.value = false;
}

function handleRefresh() {
  emit("refresh");
  fetchData();
}

onMounted(fetchData);
</script>

<template>
  <div class="table-responsive">
    <div v-if="head" class="d-flex flex-wrap mb-4 align-items-center">
      <div class="col-md-8">
        <slot></slot>
      </div>
      <div class="ms-auto mt-4 mt-md-0 input-group" style="max-width: 15rem">
        <input class="form-control" :value="modelValue" placeholder="Buscar" @input="handleInput" />
        <button class="input-group-text btn btn-success btn-icon" type="button" @click="handleRefresh">
          <i class="icon ion-md-refresh"></i>
        </button>
      </div>
    </div>
    <PulseLoader v-if="isLoading || fetchingData" class="text-center" />
    <table v-else-if="data.length" class="table table-hover table-striped">
      <thead>
        <tr class="fw-bold">
          <th v-for="(item, index) in columns" :key="index">
            {{ item }}
          </th>
        </tr>
      </thead>
      <tbody>
        <slot name="data" :rows="data"></slot>
      </tbody>
      <tfoot>
        <tr>
          <td :colspan="columns.length">
            <slot name="foot"></slot>
          </td>
        </tr>
      </tfoot>
    </table>
    <Empty v-else :title="emptytext" />
  </div>
</template>

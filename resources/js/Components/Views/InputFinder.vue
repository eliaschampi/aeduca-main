<script setup>
import { computed, inject } from "vue";
import { useEntityStore, usePlaceholder } from "../../composables/utility";
import { useBranchStore } from "../../pinia/branch";
const props = defineProps({
  who: String,
  fullname: String,
  mode: {
    type: Number,
    default: 2,
  },
  onlyCurrentReg: {
    type: Boolean,
    default: true,
  },
  onlyCurrentBranch: {
    type: Boolean,
    default: true,
  },
});

const emitter = inject("emitter");
const placeholder = computed(() => usePlaceholder(props.who));

const inputvalue = computed(() => {
  if (props.fullname) {
    return props.fullname;
  }
  const { getStore } = useEntityStore();
  const { fullname } = getStore(props.who);
  return fullname;
});

const handleShowModal = () => {
  const { who, mode, onlyCurrentReg, onlyCurrentBranch } = props;
  const { updateSfmOption } = useBranchStore();
  updateSfmOption({
    who,
    mode,
    onlyCurrentReg,
    onlyCurrentBranch,
    after: () => {
      emitter.emit("showFinderModal");
    },
  });
};
</script>

<template>
  <div class="d-flex">
    <input v-model="inputvalue" class="form-control" readonly :placeholder="placeholder" />
    <button class="btn btn-inverse-warning btn-icon" type="button" @click="handleShowModal">
      <i class="icon ion-md-search"></i>
    </button>
  </div>
</template>

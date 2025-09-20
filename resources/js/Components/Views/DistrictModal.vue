<script setup>
import { computed, reactive } from "vue";
import { useUbigeoStore } from "../../pinia/ubigeo";
import Dialog from "../Ui/Dialog.vue";

const dValue = reactive({
  depa: {},
  prov: {},
  district_code: "",
});

const emit = defineEmits(["addDistrict"]);
const { fetchUbigeo } = useUbigeoStore();
const ubigeo = computed(() => useUbigeoStore().ubigeo);

const handleSave = () => {
  const item = dValue.prov.dists.find((d) => d.code === dValue.district_code);
  if (item) {
    emit("addDistrict", item.code, item.name);
  }
};
</script>

<template>
  <Dialog
    id="district"
    :disabled="!dValue.district_code"
    title="Elija el Distrito"
    @open="fetchUbigeo"
    @ok="handleSave"
  >
    <div class="row gx-2">
      <MSelect
        v-model="dValue.depa"
        name="depa"
        standalone
        :options="ubigeo"
        label="Departamento"
        opt-value="all"
        class="col-md mb-4"
      />
      <MSelect
        v-model="dValue.prov"
        name="prov"
        :options="dValue.depa.provs"
        standalone
        label="Provincia"
        opt-value="all"
        class="col-md mb-4"
      />
    </div>
    <div class="row gx-2">
      <MSelect
        v-model="dValue.district_code"
        name="district_code"
        standalone
        :options="dValue.prov.dists"
        label="Distrito"
        class="col-md"
      />
    </div>
  </Dialog>
</template>

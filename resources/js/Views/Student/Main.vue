<script setup>
import { computed, inject, ref, watchEffect } from "vue";
import { useRoute } from "vue-router";
import MainWrapper from "../../Components/Views/MainWrapper.vue";
import NavItem from "../../Components/Views/NavItem.vue";
import { useRegisterStore } from "../../pinia/register";
import { useStudentStore } from "../../pinia/student";
import RegisterInfo from "./components/RegisterInfo.vue";
import { printCardApi } from "../../http";
import { downl } from "../../core/util";
const emitter = inject("emitter");
const { fetchRegister } = useRegisterStore();
const isLoading = ref(false);
const { updateImage } = useStudentStore();
const storedDni = computed(() => useStudentStore().dni);
const isCardPrintable = ref(false);
const routeName = computed(() => useRoute().name);

async function fetchReg(dni) {
  const register = await fetchRegister(dni);
  if (register !== null) {
    isCardPrintable.value = register.state === "a";
    if (routeName.value === "register") {
      emitter.emit("registerFetched", register);
    }
  }
}

async function printCard() {
  if (isLoading.value) return;
  isLoading.value = true;
  const { fullname } = useStudentStore();
  const { register } = useRegisterStore();
  const { data } = await printCardApi(register.code, "student");
  downl(data, `Carnet ${fullname}`);
  isLoading.value = false;
}

watchEffect(() => {
  fetchReg(storedDni.value);
});
</script>

<template>
  <MainWrapper ptype="student" @img-uploaded="updateImage">
    <template #profile-info>
      <RegisterInfo />
    </template>
    <div class="card">
      <ul class="card-header nav nav-pills nav-mtab" role="tablist">
        <NavItem title="Información" icon="ion-md-person" route="student_profile" :dni="storedDni" />
        <NavItem title="Expediente" icon="ion-md-folder" route="es_register" :dni="storedDni" />
      </ul>
      <div class="card-body">
        <div :id="routeName" class="tab-pane fade show active" role="tabpanel">
          <RouterView v-slot="{ Component }">
            <Transition name="fade" mode="out-in">
              <component :is="Component" />
            </Transition>
          </RouterView>
        </div>
      </div>
    </div>
    <template #profile-foot>
      <MButton
        v-show="isCardPrintable"
        v-can="'ANS'"
        size="btn-sm"
        color="btn-inverse-success"
        icon="icon ion-md-print"
        :disabled="isLoading"
        @ok="printCard"
      >
        {{ isLoading ? "Espere..." : "Carnet" }}
      </MButton>
    </template>
  </MainWrapper>
</template>

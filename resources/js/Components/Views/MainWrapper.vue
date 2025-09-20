<script setup>
import { computed } from "vue";
import { useEntityStore } from "../../composables/utility";
import { uploadProfileApi } from "../../http";
import { useModalStore } from "../../pinia/modal";
import { useSnackbarStore } from "../../pinia/snackbar";
import { useUserStore } from "../../pinia/user";
import Aeduca from "./Aeduca.vue";
import AvatarManager from "./AvatarManager.vue";
const { getStore } = useEntityStore();
const { successSnack } = useSnackbarStore();
const { hideModal } = useModalStore();
const props = defineProps({
  ptype: {
    type: String,
    default: "user",
  },
  pid: String,
});

const store = computed(() => {
  const { dni, fullname, image, hasProfile = false } = getStore(props.ptype);
  return { dni, fullname, image, hasProfile };
});

const emit = defineEmits(["imgUploaded"]);
const { branch_code } = useUserStore();

const personDni = computed(() => props.pid || store.value.dni);

async function handleSubmitClick(image) {
  const { ptype } = props;
  let t = ptype;
  if (/(student|teacher)/.test(ptype)) {
    t = "profile";
  } else if (ptype === "user") {
    t = "user";
  }
  const formData = new FormData();
  formData.append("image", image);
  formData.append("_method", "PUT");
  const { data } = await uploadProfileApi(t, personDni.value, formData);
  successSnack(data.message);
  emit("imgUploaded", data.filename);
  hideModal("avatarManagerModal");
}
</script>

<template>
  <div class="row">
    <div class="col-lg-4 col-md-5 col-xlg-3">
      <div v-if="personDni" class="card profile-card mb-5">
        <img alt="Portada" :src="`/default/user-info${branch_code}.jpg`" />
        <div class="avatar mx-auto">
          <img class="rounded-circle" alt="avatar" :src="store.image" />
          <MButton
            v-if="store.hasProfile"
            data-bs-toggle="modal"
            data-bs-target="#avatarManagerModal"
            color="btn-success rounded-circle btn-icon"
            icon="icon ion-md-cloud-upload"
          />
        </div>
        <div class="card-body text-center space-b-1">
          <h5>
            {{ store.fullname }}
          </h5>
          <slot name="profile-info"></slot>
        </div>
        <div class="card-footer text-center">
          <slot name="profile-foot"></slot>
        </div>
      </div>
      <slot name="room">
        <Aeduca :ptype="ptype" />
      </slot>
    </div>
    <div class="col-lg-8 col-md-7 col-xlg-9 mt-5 mt-md-0">
      <slot></slot>
    </div>
    <AvatarManager @done="handleSubmitClick" />
  </div>
</template>

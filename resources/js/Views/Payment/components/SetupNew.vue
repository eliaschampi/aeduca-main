<script setup>
import { useForm } from "vee-validate";
import { computed, reactive, ref } from "vue";
import Dialog from "../../../Components/Ui/Dialog.vue";
import months from "../../../data/months.json";
import { storePyApi } from "../../../http";
import { useModalStore } from "../../../pinia/modal";
import { useSnackbarStore } from "../../../pinia/snackbar";
const { showModal, hideModal } = useModalStore();
const { successSnack } = useSnackbarStore();
const yetPaid = ref(false);
const emit = defineEmits(["afterAdd"]);
const props = defineProps({
  cats: {
    type: Array,
  },
  registerCode: {
    type: String,
  },
  monthly: {
    type: [Number, String],
  },
});

const payment = reactive({
  pcode: "",
  description: "",
  amount: "",
  payment_type: "01",
  paid: "",
});

const schema = computed(() => {
  return {
    pcode: {
      required: !payment.code,
    },
    description: {
      required: true,
      max: 100,
    },
    amount: {
      required: true,
      natural_min: 0,
    },
    topay: {
      required: true,
    },
    paid: {
      required: yetPaid.value,
    },
  };
});

const { resetForm, handleSubmit } = useForm({
  validationSchema: schema,
});

const isMonthly = computed(() => {
  if (payment.pcode) {
    const pt = props.cats.find((item) => item.code === payment.pcode);
    return pt.name === "Mensualidad";
  }
  return false;
});

function handleShowModal(payload) {
  Object.assign(payment, payload);
  yetPaid.value = !payload.paid === false;
  showModal("apmodal");
}

function handleCancel() {
  resetForm({ errors: {} });
  payment.code = undefined;
}

async function handleAddPayment() {
  const { data } = await storePyApi({
    ...payment,
    paid: !yetPaid.value ? null : payment.paid,
    register_code: props.registerCode,
  });
  successSnack(data.message);
  hideModal("apmodal");
  emit("afterAdd", payment.code);
}

const handleOpen = () => {
  if (!payment.code) {
    payment.amount = props.monthly || 0;
  }
};

const handleSetAutoDes = (payload) => {
  const result = props.cats.find((cItem) => cItem.code === payload);
  if (result.name !== "Mensualidad") {
    payment.description = result.name;
  } else {
    payment.description = "";
  }
};

const onSubmit = handleSubmit(handleAddPayment);

defineExpose({
  handleShowModal,
});
</script>

<template>
  <Dialog
    id="apmodal"
    :title="!payment.code ? 'Agregar un nuevo pago' : 'Modificar pago'"
    btn-name="Confirmar"
    @ok="onSubmit"
    @open="handleOpen"
    @cancel="handleCancel"
  >
    <div class="space-b-1">
      <MSelect
        v-model="payment.pcode"
        name="pcode"
        label="Tipo de pago"
        :options="cats"
        @update:model-value="handleSetAutoDes"
      />
      <MSelect
        v-show="isMonthly"
        v-model="payment.description"
        name="description"
        label="Mes"
        :options="months"
        opt-value="name"
      />
      <MInput v-show="!isMonthly" v-model="payment.description" name="description" label="Concepto" />
      <div class="row gx-2 mb-2">
        <MInput
          v-model="payment.amount"
          name="amount"
          class="fw-bold col-md-6 mb-3"
          label="Monto a pagar:"
          type="number"
          step="0.01"
        />
        <div class="col-md-6">
          <label>Modalidad de pago</label>
          <div class="form-check">
            <input
              id="m01"
              v-model="payment.payment_type"
              type="radio"
              class="form-check-input"
              name="mode"
              value="01"
            />
            <label class="form-check-label" for="m01">Efectivo</label>
          </div>
          <div class="form-check">
            <input
              id="m02"
              v-model="payment.payment_type"
              type="radio"
              class="form-check-input"
              name="mode"
              value="02"
            />
            <label class="form-check-label" for="m02">Caja o banco</label>
          </div>
        </div>
      </div>
    </div>
    <MSwitch id="invoice" v-model="yetPaid" text="El pago ya ha sido realizado" />
    <div class="row gx-2 mt-3">
      <MInput v-model="payment.topay" name="topay" class="col-md-6 mb-3" label="Fecha limite" type="date" />
      <MInput v-show="yetPaid" v-model="payment.paid" name="paid" class="col-md-6" type="date" label="Fecha pagado" />
    </div>
  </Dialog>
</template>

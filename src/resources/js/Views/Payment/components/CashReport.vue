<script setup>
import { dformat } from "../../../core/day";

defineProps({
    paids: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["cancel"]);

const columns = [
    "Correlativo",
    "Modalidad",
    "Concepto",
    "Monto a Pagar",
    "Descuento",
    "Total Importe",
    "Saldo",
    "Fecha de pago",
    "#",
];

function incompleted(item) {
    return item.topay - item.paid - item.discount > 0;
}

const fulldate = (date) => dformat(date, "DD/MM/YYYY h:mm a");
</script>

<template>
    <m-table
        :columns="columns"
        :data="paids"
        :head="false"
        emptytext="El estudiante aun no ha realizado ningun pago"
    >
        <template #data="{ rows }">
            <tr
                v-for="item in rows"
                :key="item.code"
            >
                <td>{{ item.income.serie }}</td>
                <td>{{ item.actiontype.name }}</td>
                <td>{{ item.title }}</td>
                <td>{{ $currency(item.topay) }}</td>
                <td>{{ $currency(item.discount) }}</td>
                <td>{{ $currency(item.paid) }}</td>
                <td>
                    {{ $currency(item.topay - item.discount - item.paid) }}
                </td>
                <td>{{ fulldate(item.income.created_at) }}</td>
                <td
                    v-can="'AS'"
                    v-year
                >
                    <m-action
                        v-show="incompleted(item)"
                        color="danger"
                        icon="cart"
                        tool="Pagar"
                        @action="emit('cancel', item)"
                    />
                </td>
            </tr>
        </template>
    </m-table>
</template>

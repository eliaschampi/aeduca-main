<script setup>
import { ref } from "vue";
import { fetchSignHistoryApi, removeSignHistoryApi } from "../../http";
import { useSnackbarStore } from "../../pinia/snackbar";
const { showSnack, successSnack } = useSnackbarStore();
const histories = ref([]);
async function fetchHistory() {
    const { data } = await fetchSignHistoryApi();
    histories.value = data.values;
}

const getOSAndBrowserName = (userAgent) => {
    const osList = [
        { name: "Windows", regex: /Windows/ },
        { name: "Macintosh", regex: /Macintosh/ },
        { name: "Android", regex: /Android/ },
        { name: "iPhone", regex: /iPhone/ },
        { name: "iPad", regex: /iPad/ },
        { name: "Linux", regex: /Linux/ },
    ];

    const browserList = [
        { name: "Firefox", regex: /firefox\/?\s*(\d+)/i },
        { name: "Internet Explorer", regex: /(msie|trident(?=\/))\/?\s*(\d+)/i },
        { name: "Chrome", regex: /(chrome|crios)\/?\s*(\d+)/i },
        { name: "Safari", regex: /version\/?\s*(\d+).+?safari/i },
    ];

    const osName = osList.find((os) => os.regex.test(userAgent))?.name || "unknown";
    const browserName =
        browserList.find((browser) => browser.regex.test(userAgent))?.name || "Desconocido";

    return `${osName} - ${browserName}`;
};

function destroySign(item) {
    showSnack({
        text: "Eliminar acceso inmediatamente",
        button: "Confirmar",
        action: async () => {
            const { data } = await removeSignHistoryApi(item.code);
            fetchHistory();
            successSnack(data.message);
        },
    });
}

function isCurrentDevice(item) {
    return item.device === navigator.userAgent ? "Dispositivo Actual" : "Otros Dispositivos";
}
</script>

<template>
    <panel title="Inicios de sesión en otros dispositivos">
        <blockquote class="blockquote">
            Ahora puedes gestionar las sesiones iniciados en otros dispositivos, Desactiva las
            sesiones no utilizados para protejer tu informacion.
        </blockquote>
        <m-table
            :columns="['Inicio de Sesión', 'Dispositivo', 'Eliminar']"
            :data="histories"
            :fetch="fetchHistory"
            :head="false"
        >
            <template #data="{ rows }">
                <tr v-for="item in rows" :key="item.dni">
                    <td>{{ $datetim(item.created_at) }}</td>
                    <td>
                        {{ getOSAndBrowserName(item.device) }}
                        <small class="text-secondary">({{ isCurrentDevice(item) }})</small>
                    </td>
                    <td>
                        <m-action
                            icon="trash"
                            color="danger"
                            tool="Eliminar"
                            @action="destroySign(item)"
                        />
                    </td>
                </tr>
            </template>
        </m-table>
    </panel>
</template>

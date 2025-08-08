<script setup>
import { useUserStore } from "../../pinia/user";

const { user } = useUserStore();
</script>
<template>
    <panel
        title="Datos personales"
        :f="true"
    >
        <m-plain
            label="Código:"
            :value="user.code"
        />
        <m-plain
            label="DNI:"
            :value="user.dni"
        />
        <m-plain
            label="Email:"
            :value="user.email"
        />
        <m-plain
            label="Celular:"
            :value="user.phone"
        />
        <m-plain
            label="Dirección:"
            :value="user.address"
        />
        <m-plain
            label="Año:"
            :value="user.current_year"
        />
        <m-plain
            label="Nivel de acceso:"
            :value="user.access"
        />
        <template #foot>
            <m-router
                color="btn-success"
                :to="{
                    name: 'update_user',
                    params: { code: user.code },
                }"
            >
                Modificar
            </m-router>
        </template>
    </panel>
</template>

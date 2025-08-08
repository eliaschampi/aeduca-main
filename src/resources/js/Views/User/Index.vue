<script setup>
import { computed, onMounted } from "vue";
import UserCard from "../../Components/Views/UserCard.vue";
import { useUserStore } from "../../pinia/user";
const userStored = useUserStore();

const isAdmin = computed(() => userStored.can("A") && !userStored.limited);

onMounted(() => {
    userStored.fetchUsers();
});
</script>
<template>
    <section class="mt-4">
        <RouterLink
            class="btn btn-icon btn-md btn-success fixed-action-btn rounded-circle"
            :to="{ name: 'new_user' }"
        >
            <i class="icon ion-ios-add"></i>
        </RouterLink>
        <div class="row">
            <div v-if="userStored.actives.length" class="col-md-12 mygrid g18">
                <template v-for="item in userStored.actives" :key="item.code">
                    <UserCard
                        :role="item.rol.name"
                        :user="item"
                        :is-admin="isAdmin"
                        @changestate="userStored.changeState"
                    />
                </template>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-sm-12 col-md-5 col-lg-4">
                <panel title="Usuarios inactivos">
                    <m-table
                        :columns="['Usuario', 'Rol', 'Acciones']"
                        :data="userStored.inactives"
                        :head="false"
                    >
                        <template #data="{ rows }">
                            <tr v-for="item in rows" :key="item.code">
                                <td>
                                    <RouterLink
                                        :to="{
                                            name: 'update_user',
                                            params: { code: item.code },
                                        }"
                                    >
                                        {{ item.name }}...
                                    </RouterLink>
                                </td>
                                <td>{{ item.rol.name }}</td>
                                <td>
                                    <b
                                        class="text-success pointer"
                                        @click="
                                            userStored.changeState({
                                                code: item.code,
                                                state: true,
                                            })
                                        "
                                    >
                                        Activar
                                    </b>
                                </td>
                            </tr>
                        </template>
                    </m-table>
                </panel>
            </div>
        </div>
    </section>
</template>

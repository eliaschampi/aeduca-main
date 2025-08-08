<script setup>
import { useAppStore } from "../../pinia/app";
const myapp = useAppStore().myapp;
</script>
<template>
    <footer class="footer mb-4">
        <div class="container-fluid text-center">
            <div>
                <b>
                    <RouterLink class="text-success fw-bold" to="/about">
                        {{ myapp }}
                    </RouterLink>
                </b>
                {{ `${new Date().getFullYear()}` }}
                Desarrollado con <i class="icon ion-ios-heart text-danger"></i>
            </div>
        </div>
    </footer>
</template>

<script setup>
import NavLink from '@/Components/NavLink.vue'
import Button from 'primevue/button'
import Image from 'primevue/image'

import ImgSrc from '../../../img/super_admin_profile.jpg'
</script>

<template>
    <div class="min-h-[98vh] w-[200px] justify-between p-2 flex flex-col bg-gray-100 rounded-lg fixed">
        <div class="flex flex-col gap-[2rem]">
            <!-- header -->
             <div class="size-full p-2">
                <div class="w-full h-[100px] rounded-md overflow-hidden">
                    <Image :src="ImgSrc" width="200" />
                </div>
                <h1 class="text-xl">Kampung Sereh</h1>
             </div>
            <!-- header selesai -->
            <!-- side menu -->
            <div class="flex flex-col gap-[1.8rem] items-center">
                <NavLink :href="route('super_admin.dashboard')" :active="route().current('super_admin.dashboard')">
                    <i class="pi pi-home"></i><span>Dashboard</span>
                </NavLink>
                <NavLink :href="route('super_admin.pengguna')" :active="route().current(
                    'super_admin.pengguna') || route().current('super_admin.view.pengguna')">
                   <i class="pi pi-users"></i><span>Pengguna</span>
                </NavLink>
                <NavLink :href="route('super_admin.dashboard')">
                    <i class="pi pi-file"></i><span>Kriteria</span>
                </NavLink>
                <NavLink :href="route('super_admin.dashboard')">
                    <i class="pi pi-inbox"></i><span>Hasil Seleksi</span>
                </NavLink>
            </div>
        </div>
        <Button severity="danger" label="logout" icon="pi pi-power-off"/>
        <!-- side menu selesai -->
    </div>
</template>

<style scoped>
</style>    
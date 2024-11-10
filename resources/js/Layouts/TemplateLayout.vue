<script setup>
import { onMounted, ref } from 'vue';

// import component
import Sidebar from '@/Components/Sidebar.vue'
import Avatar from 'primevue/avatar'

// import sumber avatar
import ImgSrc from '../../img/super_admin_profile.jpg'

</script>

<template>
    <!-- #layout utama -->
    <div class="bg-gray-300 flex p-2 min-h-screen overflow-hidden">
        <!-- sidebar -->
         <Sidebar />
        <!-- sidebar selesai -->
         
        <!-- konten halaman -->
        <div class="w-full h-full px-2 ml-[200px]">
            <div class="flex flex-col gap-[.5rem]">
                <!-- header -->
                <div  class="bg-gray-100 rounded-lg w-full flex items-center justify-between p-2">
                    <h1 class="text-[1.3rem]">
                        Sistem Penentuan Penerima Bantuan Perbaikan Rumah
                    </h1>
                    <div>
                        <Avatar :image="ImgSrc" size="large" shape="circle" />
                    </div>
                </div>
                <!-- header selesai -->

                <!-- body -->
                <div class="bg-gray-100 rounded-lg w-full p-4 min-h-screen">
                    <slot name="pageContent"/>
                </div>
                <!-- body selesai -->
            </div>
        </div>
        <!-- konten halaman selesai -->
    </div>
    <!-- layout utama selesai -->
</template>

<style scoped>
</style>
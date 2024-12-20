<script setup>
// import component
import Sidebar from "@/Components/Sidebar.vue";
import { Avatar } from "primevue";

// import sumber avatar
import ImgSrc from "/resources/img/super_admin_profile.jpg";
;

const props = defineProps({
    auth: Object,
});

</script>

<template>
    <!-- #layout utama -->
    <div class="bg-gray-300 flex p-2 min-h-screen overflow-hidden">
        <!-- sidebar -->
        <Sidebar :auth="auth" />
        <!-- sidebar selesai -->

        <!-- konten halaman -->
        <div class="w-full h-full px-2 ml-[200px] overflow-hidden">
            <div class="flex flex-col gap-[.5rem]">
                <!-- header -->
                <div
                    class="bg-gray-100 rounded-lg w-full flex items-center justify-between p-2"
                >
                    <h1 class="text-[1.3rem] drop-shadow-md">
                        Sistem Penentuan Penerima Bantuan Perbaikan Rumah
                    </h1>
                    <div class="flex items-center p-2">
                        <p class="me-2">{{ props.auth.user.nama }}</p>
                        <i class="pi pi-user"></i>
                    </div>
                </div>
                <!-- header selesai -->

                <!-- body -->
                <div class="bg-gray-100 rounded-lg w-full p-4 min-h-screen">
                    <slot name="pageContent" />
                </div>
                <!-- body selesai -->
            </div>
        </div>
        <!-- konten halaman selesai -->
    </div>
    <!-- layout utama selesai -->
</template>

<style scoped></style>

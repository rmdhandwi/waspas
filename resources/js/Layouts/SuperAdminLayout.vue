<script setup>
import { onMounted, ref } from 'vue';

</script>

<template>
    <!-- #layout utama -->
    <div class="bg-gray-300 flex p-2 gap-2 min-h-screen overflow-hidden">
        <!-- sidebar -->
         
        <!-- sidebar selesai -->
         
        <!-- konten halaman -->
        <div class="w-full h-full bg-white rounded-lg p-4">
            <slot name="pageContent"/>
        </div>
        <!-- konten halaman selesai -->
    </div>
    <!-- layout utama selesai -->
</template>

<style scoped>
</style>
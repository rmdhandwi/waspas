<script setup>
import NavLink from "@/Components/NavLink.vue";
import Button from "primevue/button";
import Image from "primevue/image";


const props = defineProps({
    auth: Object,
});

const roleUser = props.auth.user.role;
</script>

<template>
    <div
        class="min-h-[98vh] w-[200px] justify-between p-2 flex flex-col bg-gray-100 rounded-lg fixed"
    >
        <div class="flex flex-col gap-[2rem]">
            <!-- header -->
            <div class="size-full p-2">
                <div class="w-full h-[100px] rounded-md overflow-hidden flex flex-col justify-center items-center">
                    <Image src="logo.png" width="60" />
                </div>
                <h1 class="text-xl text-center">Kampung Sereh</h1>
            </div>
            <!-- header selesai -->

            <!-- side menu super admin -->
            <div class="flex flex-col gap-[1.8rem] items-start px-2">
                <NavLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                >
                    <i class="pi pi-home"></i><span>Dashboard</span>
                </NavLink>

                <!-- Menu khusus untuk super_admin -->
                <template v-if="roleUser === 'perangkat'">
                    <NavLink
                        :href="route('super_admin.pengguna')"
                        :active="
                            route().current('super_admin.pengguna') ||
                            route().current('super_admin.view.pengguna')
                        "
                    >
                        <i class="pi pi-users"></i><span>Pengguna</span>
                    </NavLink>

                    <NavLink
                        :href="route('periode')"
                        :active="route().current('periode')"
                    >
                        <i class="pi pi-calendar"></i><span>Periode</span>
                    </NavLink>

                    <NavLink
                        :href="route('super_admin.kriteria')"
                        :active="
                            route().current('super_admin.kriteria') ||
                            route().current('super_admin.view.sub_kriteria')
                        "
                    >
                        <i class="pi pi-list"></i><span>Kriteria</span>
                    </NavLink>
                </template>

                <!-- Menu umum untuk semua role -->
                <template
                    v-if="roleUser === 'perangkat' || roleUser === 'kepala'"
                >
                    <NavLink
                        :href="route('wargapage')"
                        :active="
                            route().current('wargapage') ||
                            route().current('seleksi')
                        "
                    >
                        <i class="pi pi-inbox"></i><span>Data Warga</span>
                    </NavLink>
                </template>
                <template
                    v-if="roleUser === 'perangkat' || roleUser === 'kepala' || roleUser === 'warga'"
                >
                    <NavLink
                        :href="route('hasil')"
                        :active="route().current('hasil')"
                    >
                        <i class="pi pi-chart-bar"></i
                        ><span>Hasil Seleksi</span>
                    </NavLink>
                </template>
            </div>
        </div>

        <!-- Logout button -->
        <Button
            as="a"
            severity="danger"
            @click="deleteLocalStorage()"
            label="Logout"
            icon="pi pi-power-off"
            :href="route('logout')"
        />
    </div>
</template>

<style scoped></style>

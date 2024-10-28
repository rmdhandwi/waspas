<script setup>
import { onMounted,ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
// import layout
import Layout from '@/Layouts/SuperAdminLayout.vue'

// import component
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';
import Password from 'primevue/password';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";

const props = defineProps({ userData : Object})

let jkel = [{jenis : "Laki-laki"},{jenis:"Perempuan"}]

const confirm = useConfirm()

const userForm = useForm({
    id : props.userData.id,
    username : props.userData.username,
    nama : props.userData.nama,
    tgl_lahir : props.userData.tgl_lahir,
    jkel : props.userData.jkel,
    email : props.userData.email,
    // password : props.userData.password,
    foto_profil : props.userData.foto_profil,
    no_telp : props.userData.no_telp,
    alamat : props.userData.alamat,
    role : props.userData.role,
})

const updateUser = () => 
{
    confirm.require({
        message: `Update data pengguna : ${userForm.nama} ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Konfirmasi',
            severity: 'success'
        },
        accept: () => {
            userForm.post(`/super_admin/Pengguna/update/${userForm.id}`)
        },

    });
}

</script>

<template>
    <Head title="Detail Pengguna"/>
    <Layout>
        <template #pageContent>
            <!-- modal update -->
            <ConfirmDialog></ConfirmDialog>
            <!-- modal update selesai -->
            <div class="flex flex-col gap-[1rem]">
                <!-- header -->
                <div class="p-4">
                    <h1 class="text-lg">Detail Pengguna</h1>
                </div>
                <!-- header selesai -->

                <form @submit.prevent="updateUser()" class="flex flex-col gap-[2rem] p-4" autocomplete="off">
                    <div class="flex flex-wrap items-center gap-[2rem]">
                        <div class="flex flex-col gap-2">
                            <label for="username" class="font-medium" >Username</label>
                            <InputText v-model="userForm.username" id="username" autocomplete="off" :value="userForm.username" class="w-[20rem]"  />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="nama" class="font-medium">Nama</label>
                            <InputText v-model="userForm.nama" id="nama" autocomplete="off" :value="userForm.nama" class="w-[20rem]"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="tglLahir" class="font-medium">Tanggal Lahir</label>
                            <DatePicker v-model="userForm.tgl_lahir" id="tglLahir" autocomplete="off" :modelValue="userForm.tgl_lahir" class="w-[20rem]"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="jkel" class="font-medium">Jenis Kelamin</label>
                            <Select id="jkel" v-model="userForm.jkel" :options="jkel" optionValue="jenis" optionLabel="jenis" class="w-[20rem]"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="email" class="font-medium">Email</label>
                            <InputText v-model="userForm.email" id="email" autocomplete="off" :value="userForm.email" class="w-[20rem]"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="no_telp" class="font-medium">No Telp</label>
                            <InputText v-model="userForm.no_telp" id="no_telp" autocomplete="off" :value="userForm.no_telp" class="w-[20rem]"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="alamat" class="font-medium">Alamat</label>
                            <InputText v-model="userForm.alamat" id="alamat" autocomplete="off" :value="userForm.alamat" :placeholder="userForm === null ? userForm.alamat : 'Masukkan Alamat'" class="w-[20rem]"/>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <Button type="submit" @click="updateUser()" severity="info" label="Update" />
                        <Button severity="danger" label="Batal" />
                    </div>
                </form>
            </div>
        </template>
    </Layout>
</template>

<style scoped>
</style>
<script setup>
import { onMounted,ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
// import layout
import Layout from '@/Layouts/SuperAdminLayout.vue'
// import component
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Image from 'primevue/image';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';
import Password from 'primevue/password';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast"

import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import NavLink from '@/Components/NavLink.vue';


let props = defineProps({ usersData : Object, flash : Object})

onMounted(() => 
{
    dataUserFix.value = props.usersData.map((p, i) => ({ index : i+1, ...p}))
    checkNotif()
})

let dataUserFix = ref([])

let showForm = ref(false)

let jkel = [{jenis : 'Laki-Laki'}, {jenis : 'Perempuan'}]

const userForm = useForm({
    username : null,
    nama : null,
    tgl_lahir : null,
    jkel : null,
    email : null,
    password : null,
    foto_profil : null,
    no_telp : null,
    alamat : null,
    role : 'admin',
})

const toast = useToast()
const confirm = useConfirm()

const checkNotif = () =>
{
    if(props.flash.notif_status)
    {
        setTimeout(() =>
        {
            if(props.flash.notif_status === 'success') {
                toast.add({ severity: 'success', summary: 'Info', detail: props.flash.notif_message, life: 4000,  group : 'tc' });
            }
            else{
                toast.add({ severity: 'error', summary: 'Info', detail: props.flash.notif_message, life: 4000,  group : 'tc' });
            }
        },1000)
    }
}

const isLoading = ref(false)

const refreshPage = () =>
{
    checkNotif()
    showForm.value = false
    isLoading.value = true
    router.visit(route('super_admin.pengguna'))
    userForm.reset()
    hapusUserData.reset()
    setTimeout(() => isLoading.value = false, 600)
}

const submitData = () =>
{
    userForm.post(route('super_admin.tambah.pengguna'), 
    {
        onSuccess : () => refreshPage()  
    })
}

let hapusUserData = useForm({
    id : null,
    nama: null
})

let hapusUser = (idUser,nama) =>
{
    hapusUserData.id = idUser
    hapusUserData.nama = nama

    confirm.require({
        message: `Yakin ingin menghapus data pengguna : ${nama} ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Hapus',
            severity: 'danger'
        },
        accept: () => {
            hapusUserData.post(`/super_admin/Pengguna/hapus/${idUser}`, 
            {
                onSuccess : () => refreshPage() 
            })
        },

    });
}

</script>

<template>
    <Head title="Pengguna"/>
    <Layout>
        <template #pageContent>
            <div class="flex flex-col gap-[1rem]">
                <Toast position="top-center" group="tc"/>
                <div class="p-4 flex justify-between items-center">
                    <h1 class="text-lg">Daftar Pengguna</h1>
                    <Button severity="success" size="small" label="Tambah Pengguna" @click="showForm = true"/>
                </div>
                
                <!-- modal tambah pengguna -->
                <Dialog v-model:visible="showForm" modal header="Tambah Pengguna" :style="{ width: '40rem' }">
                    <form @submit.prevent="submitData" class="flex flex-col gap-y-2" autocomplete="off">
                        <input type="hidden" autocomplete="false"/>
                        <!-- data form -->
                        <div class="flex items-center gap-4 my-4">
                            <label for="username" class="font-semibold w-40">Username</label>
                            <InputText v-model="userForm.username" id="username" class="flex-auto" autocomplete="off" placeholder="Masukkan username" />
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="nama" class="font-semibold w-40">Nama</label>
                            <InputText v-model="userForm.nama" id="nama" class="flex-auto" autocomplete="off" placeholder="Masukkan nama" />
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="email" class="font-semibold w-40">Email</label>
                            <InputText v-model="userForm.email" id="email" class="flex-auto" autocomplete="off" placeholder="Masukkan email" />
                        </div>
                       
                        <div class="flex items-center gap-4 my-4">
                            <label for="tglLahir" class="font-semibold w-40">Tanggal Lahir</label>
                            <DatePicker v-model="userForm.tgl_lahir" class="flex-auto"  id="tglLahir" showIcon fluid showButtonBar iconDisplay="input" dateFormat="yy-mm-dd" placeholder="Masukkan Tanggal lahir" />
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="jkel" class="font-semibold w-40">Jenis Kelamin</label>
                            <Select id="jkel" v-model="userForm.jkel" :options="jkel" optionValue="jenis" optionLabel="jenis" placeholder="Laki-Laki / Perempuan" class="flex-auto" />    
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="password" class="font-semibold w-40">Password</label>
                            <Password v-model="userForm.password" id="password" autocomplete="off" placeholder="Masukkan password" promptLabel="Masukkan Password" weakLabel="lemah" mediumLabel="lumayan" strongLabel="bagus" toggleMask/>
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="no_telp" class="font-semibold w-40">No Telp</label>
                            <InputText v-model="userForm.no_telp" id="no_telp" class="flex-auto" autocomplete="off" placeholder="Masukkan No Telepon" />
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="alamat" class="font-semibold w-40">Alamat</label>
                            <InputText v-model="userForm.alamat" id="alamat" class="flex-auto" autocomplete="off" placeholder="Masukkan Alamat" />
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button type="button" label="Reset" outlined severity="danger" @click="userForm.reset()"/>
                            <Button type="submit" label="Simpan Data" outlined severity="info"/>
                        </div>

                        <!-- data form selesai -->
                    </form>
                </Dialog>
                <!-- modal tambah pengguna selesai -->
                <!-- modal hapus pengguna  -->
                <ConfirmDialog></ConfirmDialog>
                <!-- modal hapus pengguna selesai  -->
                <Card>
                    <template #content>
                        <DataTable :value="dataUserFix" paginator :rows="10">
                            <Column sortable field="index" header="No"/>
                            <Column sortable field="username" header="Username"/>
                            <Column sortable field="nama" header="Nama"/>
                            <Column field="role" header="Jabatan"/>
                            <Column header="Foto">
                                <template #body="{data}">
                                    <Image preview :alt="data.nama" :src="data.foto_profil" width="100px"v-if="data.foto_path"/>
                                    <span class="text-gray-400 text-sm" v-else>
                                        Tidak Ada Foto
                                    </span>
                                </template>
                            </Column>
                            <Column header="Opsi">
                                <template #body="{data}">
                                    <div class="flex gap-2 items-center">
                                        <NavLink class="border-none p-0 m-0" :href="`Pengguna/view/${data.id}`">
                                            <Button size="small" icon="pi pi-pen-to-square" iconPos="right" severity="info" outlined />
                                        </NavLink>
                                        <Button @click="hapusUser(data.id, data.nama)" size="small" icon="pi pi-trash" iconPos="right" severity="danger" outlined/>
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                </Card>

            </div>
        </template>
    </Layout>
</template>

<style scoped>
</style>
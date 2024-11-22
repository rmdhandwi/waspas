<script setup>
import { onMounted, ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import TemplateLayout from '@/Layouts/TemplateLayout.vue'

// import komponen prime 
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Select from 'primevue/select'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from "primevue/useconfirm"
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import {FilterMatchMode} from '@primevue/core/api' 
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'


onMounted(() =>
{
    checkNotif()
    data_warga_fix.value = pageProps.data_warga.map((p,i) => ({index : i+1, ...p}))
})

const pageProps = defineProps({ data_warga : Object ,flash : Object})

const showForm = ref(false)

const showFormUbah = ref(false)

const confirm = useConfirm()

const toast = useToast()

const isLoading = ref(false)

const data_warga_fix = ref([])

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
let dt = ref()


let pekerjaan = [
    { nama : 'Pegawai Negeri Sipil'},
    { nama : 'Wiraswasta'},
    { nama : 'Polisi'},
    { nama : 'TNI'},
]

let sanitasi = [
    { pilihan : 'Milik Sendiri'},
    { pilihan : 'WC Umum'},
    { pilihan : 'Tidak Memiliki WC'},
]

let t_limbah = [
    { tempat : 'Bukan Septictank'},
    { tempat : 'IPAL'},
    { tempat : 'Komunal'},
    { tempat : 'Septictank Pribadi'},
]

let jenis_wc = [
    { jenis : 'WC Duduk'},
    { jenis : 'WC Jongkok'},
]

let akses_air = [
    { akses : 'Galon'},
    { akses : 'Masak Air'},
]

let agama = [
    { nama : 'Budha',value : 'Bu'},
    { nama : 'Hindu', value : 'Hi'},
    { nama : 'Islam', value : 'Is'},
    { nama : 'Kristen Protestan', value : 'KP'},
    { nama : 'Kristen Katolik', value : 'KK'},
    { nama : 'Konghucu', value : 'Ko'},
]

let status_rumah = [
    { status : 'Bantuan Pemerintah'},
    { status : 'Kontrak'},
    { status : 'Milik Orang Lain'},
    { status : 'Milik Sendiri'},
    { status : 'Numpang'},
    { status : 'Sewa'},
]

let struktur_bangunan = [
    { struktur : 'Non Permanen (Gubuk)'},
    { struktur : 'Permanen'},
    { struktur : 'Semi Permanen'},
]

let jkel = [{jenis : 'Laki-Laki', value : 'L'}, {jenis : 'Perempuan', value : 'P'}]

const checkNotif = () =>
{
    if(pageProps.flash.notif_status)
    {
        setTimeout(() =>
        {
            toast.add({ severity: pageProps.flash.notif_status, summary: 'Notifikasi', detail: pageProps.flash.notif_message, life: 4000,  group : 'tc' });
        },1000)
    }
}

const refreshPage = () =>
{
    checkNotif()
    showForm.value = false
    isLoading.value = true
    router.visit(route('admin.data_warga'))
    setTimeout(() => isLoading.value = false, 600)
}

const dataWarga = useForm(
{
    nomor_kk : null,
    nama_kk : null,
    provinsi : 'Papua',
    kabupaten : 'Jayapura',
    kampung : 'Sereh',
    no_rt : null,
    no_rw : null,
    asal_suku : null,
    pekerjaan : null,
    agama : null,
    jenis_kelamin : null,
    sanitasi : null,
    j_kloset : null,
    t_limbah : null,
    akses_air_minum : null,
    status_rumah : null,
    struktur_bangunan : null
})
const dataWargaEdit = useForm(
{
    id : null,
    nomor_kk : null,
    nama_kk : null,
    provinsi : 'Papua',
    kabupaten : 'Jayapura',
    kampung : 'Sereh',
    no_rt : null,
    no_rw : null,
    asal_suku : null,
    pekerjaan : null,
    agama : null,
    jenis_kelamin : null,
    sanitasi : null,
    j_kloset : null,
    t_limbah : null,
    akses_air_minum : null,
    status_rumah : null,
    struktur_bangunan : null
})

const tambahData = () =>
{
    confirm.require({
        message: `Simpan perubahan ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Simpan',
            severity: 'info'
        },
        accept: () => {
            showForm.value = false
            dataWarga.post(route('admin.tambah.data_warga'), 
            {
                onSuccess : () => refreshPage() ,
                onError : () => { 
                    setTimeout(() =>
                    {
                        toast.add({ severity: 'error', summary: 'Notifikasi', detail: 'Gagal menambahkan Data :(', life: 4000,  group : 'tc' });
                    },500)
                    showForm.value = true 
                }
            })
        },

    })
}

const lihatData = (idx) =>
{
    showFormUbah.value = true
    dataWargaEdit.id = data_warga_fix.value[idx-1]['id']
    dataWargaEdit.nomor_kk = data_warga_fix.value[idx-1]['nomor_kk']
    dataWargaEdit.nama_kk = data_warga_fix.value[idx-1]['nama_kk']
    dataWargaEdit.no_rt = data_warga_fix.value[idx-1]['rt']
    dataWargaEdit.no_rw = data_warga_fix.value[idx-1]['rw']
    dataWargaEdit.asal_suku = data_warga_fix.value[idx-1]['asal_suku']
    dataWargaEdit.pekerjaan = data_warga_fix.value[idx-1]['pekerjaan']
    dataWargaEdit.agama = data_warga_fix.value[idx-1]['agama']
    dataWargaEdit.jenis_kelamin = data_warga_fix.value[idx-1]['jenis_kelamin']
    dataWargaEdit.sanitasi = data_warga_fix.value[idx-1]['sanitasi']
    dataWargaEdit.j_kloset = data_warga_fix.value[idx-1]['j_kloset']
    dataWargaEdit.t_limbah = data_warga_fix.value[idx-1]['t_limbah']
    dataWargaEdit.akses_air_minum = data_warga_fix.value[idx-1]['akses_air_minum']
    dataWargaEdit.status_rumah = data_warga_fix.value[idx-1]['status_rumah']
    dataWargaEdit.struktur_bangunan = data_warga_fix.value[idx-1]['struktur_bangunan']
}

const updateData = (id) => 
{
    confirm.require({
        message: `Simpan Perubahan Data ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Ya',
            severity: 'info'
        },
        accept: () => {
            showFormUbah.value = false
            dataWargaEdit.post(`/admin/Warga/update/${id}`, 
            {
                onSuccess : () => refreshPage() ,
                onError : () => { 
                    setTimeout(() =>
                    {
                        toast.add({ severity: 'error', summary: 'Notifikasi', detail: 'Gagal Update Data :(', life: 4000,  group : 'tc' });
                    },500)
                    showFormUbah.value = true 
                }
            }
            )
        },

    })
}

const hapusData = (id, nama) =>
{
    dataWargaEdit.id = id
    dataWargaEdit.nama_kk = nama

    confirm.require({
        message: `Hapus Data Rumah Tangga : ${nama} ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Hapus',
            severity: 'info'
        },
        accept: () => {
            dataWargaEdit.post(`/admin/Warga/hapus/${id}`, 
            {
                onSuccess : () => refreshPage() ,
                onError : () => { 
                    setTimeout(() =>
                    {
                        toast.add({ severity: 'error', summary: 'Notifikasi', detail: 'Gagal Hapus Data :(', life: 4000,  group : 'tc' });
                    },500)
                }
            }
            )
        },

    })

    console.log(dataWargaEdit.id)
    console.log(dataWargaEdit.nama_kk)
}

const exportCSV = () =>
{
    dt.value.exportCSV()
}

</script>

<template>
    <Head title="Data Warga"/>
    <Toast position="top-center" group="tc"/>
    <ConfirmDialog style="width: 24rem;"/>
    <TemplateLayout>
        <template #pageContent>
            <div class="flex flex-col gap-[1rem] p-4">
                <div class="flex justify-between items-center">
                    <Button class="bg-white rounded py-2 px-4" disabled label="Data Rumah Tangga Warga" unstyled/>
                    <div class="flex items-center justify-between">
                        <Button @click="showForm = true" severity="success" label="Tambah Data" size="small" class="w-[12rem]"/>
                    </div>
                </div>

                <!-- dialog tambah data warga -->
                <Dialog modal header="Tambah Data Warga" :style="{width: '90%'}" v-model:visible="showForm">
                    <form @submit.prevent="tambahData" class="flex flex-col gap-y-[8rem]" autocomplete="off">
                        <!-- data form -->
                         <div class="flex flex-wrap">
                            <!-- kiri -->
                            <div class="flex flex-wrap gap-4 gap-x-8 w-[36rem]">
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="nomor_kk" class="font-semibold">Nomor Kartu Keluarga</label>
                                    <InputText v-model="dataWarga.nomor_kk" id="nomor_kk"  autocomplete="off" placeholder="Masukkan no kartu keluarga" :invalid="dataWarga.errors.nomor_kk?true:false"/>
                                    <span class="text-red-500" v-if="dataWarga.errors.nomor_kk">{{ dataWarga.errors.nomor_kk }}</span>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="nama_kk" class="font-semibold">Nama Kepala Keluarga</label>
                                    <InputText v-model="dataWarga.nama_kk" id="nama_kk"  autocomplete="off" placeholder="Masukkan nama kepala keluarga" :invalid="dataWarga.errors.nama_kk?true:false" />
                                    <span class="text-red-500" v-if="dataWarga.errors.nama_kk">{{ dataWarga.errors.nama_kk }}</span>
                                </div>
        
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="provinsi" class="font-semibold">Provinsi</label>
                                    <InputText v-model="dataWarga.provinsi" id="provinsi"  placeholder="Papua" disabled/>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="kabupaten" class="font-semibold">Kabupaten</label>
                                    <InputText v-model="dataWarga.kabupaten" id="kabupaten"  placeholder="Jayapura" disabled/>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="kampung" class="font-semibold">Kampung</label>
                                    <InputText v-model="dataWarga.kampung" id="kampung" placeholder="Sereh" disabled/>
                                </div>
        
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="no_rt" class="font-semibold">RT</label>
                                    <InputText v-model="dataWarga.no_rt" id="no_rt" placeholder="Masukkan RT" :invalid="dataWarga.errors.no_rt?true:false" />
                                    <span class="text-red-500" v-if="dataWarga.errors.no_rt">{{ dataWarga.errors.no_rt }}</span>
                                </div>
        
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="no_rw" class="font-semibold">RW</label>
                                    <InputText v-model="dataWarga.no_rw" id="no_rw" placeholder="Masukkan RW"  :invalid="dataWarga.errors.no_rw?true:false"/>
                                    <span class="text-red-500" v-if="dataWarga.errors.no_rw">{{ dataWarga.errors.no_rw }}</span>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="asal_suku" class="font-semibold">Asal Suku</label>
                                    <InputText v-model="dataWarga.asal_suku" id="asal_suku" placeholder="Masukkan asal suku"  :invalid="dataWarga.errors.asal_suku?true:false"/>
                                    <span class="text-red-500" v-if="dataWarga.errors.asal_suku">{{ dataWarga.errors.asal_suku }}</span>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[94%]">
                                    <label for="t_limbah" class="font-semibold">Tempat Pembuangan Limbah Tinja</label>
                                    <Select id="t_limbah" v-model="dataWarga.t_limbah" :options="t_limbah" optionValue="tempat" optionLabel="tempat" placeholder="Pilih Tempat Pembuangan Limbah Tinja" :invalid="dataWarga.errors.t_limbah?true:false"/>
                                    <span class="text-red-500" v-if="dataWarga.errors.t_limbah">{{ dataWarga.errors.t_limbah }}</span>
                                </div>
     
                            </div>
                            <!-- kanan -->
                            <div class="flex flex-wrap gap-4 w-[36rem] h-0">
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="pekerjaan" class="font-semibold">Pekerjaan</label>
                                     <Select id="pekerjaan" @keyup="console.log(dataWarga.pekerjaan)" editable v-model="dataWarga.pekerjaan" :options="pekerjaan" optionValue="nama" optionLabel="nama" :invalid="dataWarga.errors.pekerjaan?true:false" placeholder="Pilih Pekerjaan"/>
                                     <span class="text-red-500" v-if="dataWarga.errors.pekerjaan">{{ dataWarga.errors.pekerjaan }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="agama" class="font-semibold">Agama</label>
                                     <Select id="agama" v-model="dataWarga.agama" :options="agama" optionValue="value" optionLabel="nama" placeholder="Pilih Agama" :invalid="dataWarga.errors.agama?true:false"/>
                                     <span class="text-red-500" v-if="dataWarga.errors.agama">{{ dataWarga.errors.agama }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="jkel" class="font-semibold">Jenis Kelamin</label>
                                     <Select id="jkel" v-model="dataWarga.jenis_kelamin" :options="jkel" optionValue="value" optionLabel="jenis" placeholder="Pilih Jenis Kelamin" :invalid="dataWarga.errors.jenis_kelamin?true:false"/>
                                     <span class="text-red-500" v-if="dataWarga.errors.jenis_kelamin">{{ dataWarga.errors.jenis_kelamin }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="sanitasi" class="font-semibold">Sanitasi</label>
                                     <Select id="sanitasi" v-model="dataWarga.sanitasi" :options="sanitasi" optionValue="pilihan" optionLabel="pilihan" placeholder="Pilih Sanitasi" :invalid="dataWarga.errors.sanitasi?true:false"/>
                                     <span class="text-red-500" v-if="dataWarga.errors.sanitasi">{{ dataWarga.errors.sanitasi }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="jenis_wc" class="font-semibold">Jenis WC</label>
                                     <Select id="jenis_wc" v-model="dataWarga.j_kloset" :options="jenis_wc" optionValue="jenis" optionLabel="jenis" placeholder="Pilih Jenis WC" :invalid="dataWarga.errors.j_kloset?true:false"/>
                                     <span class="text-red-500" v-if="dataWarga.errors.j_kloset">{{ dataWarga.errors.j_kloset }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="akses_air" class="font-semibold">Akses Air Minum</label>
                                     <Select id="akses_air" v-model="dataWarga.akses_air_minum" :options="akses_air" optionValue="akses" optionLabel="akses" placeholder="Pilih Akses Air Minum" :invalid="dataWarga.errors.akses_air_minum?true:false"/>
                                     <span class="text-red-500" v-if="dataWarga.errors.akses_air_minum">{{ dataWarga.errors.akses_air_minum }}</span>
                                 </div>
    
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="status_rumah" class="font-semibold">Status Kepemilikan Rumah</label>
                                     <Select id="status_rumah" v-model="dataWarga.status_rumah" :options="status_rumah" optionValue="status" optionLabel="status" placeholder="Pilih Status Kepemilikan Rumah" :invalid="dataWarga.errors.status_rumah?true:false"/>
                                     <span class="text-red-500" v-if="dataWarga.errors.status_rumah">{{ dataWarga.errors.status_rumah }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="struktur_bangunan" class="font-semibold">Struktur Bangunan</label>
                                     <Select id="struktur_bangunan" v-model="dataWarga.struktur_bangunan" :options="struktur_bangunan" optionValue="struktur" optionLabel="struktur" placeholder="Pilih Struktur Bangunan" :invalid="dataWarga.errors.struktur_bangunan?true:false"/>
                                     <span class="text-red-500" v-if="dataWarga.errors.struktur_bangunan">{{ dataWarga.errors.struktur_bangunan }}</span>
                                 </div>
     
                            </div>
                         </div>

                        <Button severity="success" class="w-full" label="Simpan Data" type="submit"/>
                    </form>
                </Dialog>
                <!-- dialog ubah data warga -->
                <Dialog modal header="Ubah Data Warga" :style="{width: '90%'}" v-model:visible="showFormUbah">
                    <form @submit.prevent="updateData(dataWargaEdit.id)" class="flex flex-col gap-y-[8rem]" autocomplete="off">
                        <!-- data form -->
                         <div class="flex flex-wrap">
                            <!-- kiri -->
                            <div class="flex flex-wrap gap-4 gap-x-8 w-[36rem]">
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="nomor_kk" class="font-semibold">Nomor Kartu Keluarga</label>
                                    <InputText v-model="dataWargaEdit.nomor_kk" id="nomor_kk"  autocomplete="off" placeholder="Masukkan no kartu keluarga" :invalid="dataWargaEdit.errors.nomor_kk?true:false" />
                                    <span class="text-red-500" v-if="dataWargaEdit.errors.nomor_kk">{{ dataWargaEdit.errors.nomor_kk }}</span>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="nama_kk" class="font-semibold">Nama Kepala Keluarga</label>
                                    <InputText v-model="dataWargaEdit.nama_kk" id="nama_kk"  autocomplete="off" placeholder="Masukkan nama kepala keluarga" :invalid="dataWargaEdit.errors.nama_kk?true:false" />
                                    <span class="text-red-500" v-if="dataWargaEdit.errors.nama_kk">{{ dataWargaEdit.errors.nama_kk }}</span>
                                </div>
        
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="provinsi" class="font-semibold">Provinsi</label>
                                    <InputText id="provinsi"  placeholder="Papua" disabled/>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="kabupaten" class="font-semibold">Kabupaten</label>
                                    <InputText id="kabupaten"  placeholder="Jayapura" disabled/>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="kampung" class="font-semibold">Kampung</label>
                                    <InputText id="kampung" placeholder="Sereh" disabled/>
                                </div>
        
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="no_rt" class="font-semibold">RT</label>
                                    <InputText v-model="dataWargaEdit.no_rt" id="no_rt" placeholder="Masukkan RT" :invalid="dataWargaEdit.errors.no_rt?true:false" />
                                    <span class="text-red-500" v-if="dataWargaEdit.errors.no_rt">{{ dataWargaEdit.errors.no_rt }}</span>
                                </div>
        
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="no_rw" class="font-semibold">RW</label>
                                    <InputText v-model="dataWargaEdit.no_rw" id="no_rw" placeholder="Masukkan RW"  :invalid="dataWargaEdit.errors.no_rw?true:false"/>
                                    <span class="text-red-500" v-if="dataWargaEdit.errors.no_rw">{{ dataWargaEdit.errors.no_rw }}</span>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[16rem]">
                                    <label for="asal_suku" class="font-semibold">Asal Suku</label>
                                    <InputText v-model="dataWargaEdit.asal_suku" id="asal_suku" placeholder="Masukkan asal suku"  :invalid="dataWargaEdit.errors.asal_suku?true:false"/>
                                    <span class="text-red-500" v-if="dataWargaEdit.errors.asal_suku">{{ dataWargaEdit.errors.asal_suku }}</span>
                                </div>
    
                                <div class="flex flex-col gap-4 w-[94%]">
                                    <label for="t_limbah" class="font-semibold">Tempat Pembuangan Limbah Tinja</label>
                                    <Select id="t_limbah" v-model="dataWargaEdit.t_limbah" :options="t_limbah" optionValue="tempat" optionLabel="tempat" placeholder="Pilih Tempat Pembuangan Limbah Tinja" :invalid="dataWargaEdit.errors.t_limbah?true:false"/>
                                    <span class="text-red-500" v-if="dataWargaEdit.errors.t_limbah">{{ dataWargaEdit.errors.t_limbah }}</span>
                                </div>
     
                            </div>
                            <!-- kanan -->
                            <div class="flex flex-wrap gap-4 w-[36rem] h-0">
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="pekerjaan" class="font-semibold">Pekerjaan</label>
                                     <Select id="pekerjaan" editable v-model="dataWargaEdit.pekerjaan" :options="pekerjaan" optionValue="nama" optionLabel="nama" :invalid="dataWargaEdit.errors.pekerjaan?true:false" placeholder="Pilih Pekerjaan"/>
                                     <span class="text-red-500" v-if="dataWargaEdit.errors.pekerjaan">{{ dataWargaEdit.errors.pekerjaan }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="agama" class="font-semibold">Agama</label>
                                     <Select id="agama" v-model="dataWargaEdit.agama" :options="agama" optionValue="value" optionLabel="nama" placeholder="Pilih Agama" :invalid="dataWargaEdit.errors.agama?true:false"/>
                                     <span class="text-red-500" v-if="dataWargaEdit.errors.agama">{{ dataWargaEdit.errors.agama }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="jkel" class="font-semibold">Jenis Kelamin</label>
                                     <Select id="jkel" v-model="dataWargaEdit.jenis_kelamin" :options="jkel" optionValue="value" optionLabel="jenis" placeholder="Pilih Jenis Kelamin" :invalid="dataWargaEdit.errors.jenis_kelamin?true:false"/>
                                     <span class="text-red-500" v-if="dataWargaEdit.errors.jenis_kelamin">{{ dataWargaEdit.errors.jenis_kelamin }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="sanitasi" class="font-semibold">Sanitasi</label>
                                     <Select id="sanitasi" v-model="dataWargaEdit.sanitasi" :options="sanitasi" optionValue="pilihan" optionLabel="pilihan" placeholder="Pilih Sanitasi" :invalid="dataWargaEdit.errors.sanitasi?true:false"/>
                                     <span class="text-red-500" v-if="dataWargaEdit.errors.sanitasi">{{ dataWargaEdit.errors.sanitasi }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="jenis_wc" class="font-semibold">Jenis WC</label>
                                     <Select id="jenis_wc" v-model="dataWargaEdit.j_kloset" :options="jenis_wc" optionValue="jenis" optionLabel="jenis" placeholder="Pilih Jenis WC" :invalid="dataWargaEdit.errors.j_kloset?true:false"/>
                                     <span class="text-red-500" v-if="dataWargaEdit.errors.j_kloset">{{ dataWargaEdit.errors.j_kloset }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="akses_air" class="font-semibold">Akses Air Minum</label>
                                     <Select id="akses_air" v-model="dataWargaEdit.akses_air_minum" :options="akses_air" optionValue="akses" optionLabel="akses" placeholder="Pilih Akses Air Minum" :invalid="dataWarga.errors.akses_air_minum?true:false"/>
                                     <span class="text-red-500" v-if="dataWarga.errors.akses_air_minum">{{ dataWarga.errors.akses_air_minum }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="status_rumah" class="font-semibold">Status Kepemilikan Rumah</label>
                                     <Select id="status_rumah" v-model="dataWargaEdit.status_rumah" :options="status_rumah" optionValue="status" optionLabel="status" placeholder="Pilih Status Kepemilikan Rumah" :invalid="dataWargaEdit.errors.status_rumah?true:false"/>
                                     <span class="text-red-500" v-if="dataWargaEdit.errors.status_rumah">{{ dataWargaEdit.errors.status_rumah }}</span>
                                 </div>
     
                                 <div class="flex flex-col gap-4 w-[16rem]">
                                     <label for="struktur_bangunan" class="font-semibold">Struktur Bangunan</label>
                                     <Select id="struktur_bangunan" v-model="dataWargaEdit.struktur_bangunan" :options="struktur_bangunan" optionValue="struktur" optionLabel="struktur" placeholder="Pilih Struktur Bangunan" :invalid="dataWargaEdit.errors.struktur_bangunan?true:false"/>
                                     <span class="text-red-500" v-if="dataWargaEdit.errors.struktur_bangunan">{{ dataWargaEdit.errors.struktur_bangunan }}</span>
                                 </div>
     
                            </div>
                         </div>

                        <Button severity="success" class="w-full" label="Update Data" type="submit"/>
                    </form>
                </Dialog>

                <!-- datatable warga -->
                <DataTable ref="dt" v-model:filters="filters" :value="data_warga_fix" stripedRows paginator :rows="10" scrollable removableSort>
                    <template #header>
                        <div class="flex items-center justify-between">
                            <Button icon="pi pi-external-link" label="Export" @click="exportCSV($event)" size="small"/>
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search me-4" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Cari Data Warga" />
                            </IconField>
                        </div>
                    </template>
                    <template #empty>
                        <span class="flex justify-center items-center text-lg">Tidak ada data</span>
                    </template>
                    <Column sortable field="index" header="No"/>
                    <Column sortable field="nomor_kk" header="Nomor KK"/>
                    <Column sortable field="nama_kk" header="Nama Kepala Keluarga"/>
                    <Column sortable field="status_rumah" header="Status Kepemilikan Rumah"/>
                    <Column sortable field="struktur_bangunan" header="Struktur Bangunan Rumah"/>
                    <Column sortable field="sanitasi" header="Kepemilikan Sanitasi"/>
                    <Column sortable field="t_limbah" header="Tempat Limbah Tinja Dibuang"/>
                    <Column header="Opsi" frozen alignFrozen="right" class="min-w-[4rem]">
                        <template #body="{data}">
                            <div class="flex gap-2 items-center">
                                <Button size="small" icon="pi pi-pen-to-square" @click="lihatData(data.index)" iconPos="right" severity="info" outlined/>
                                <Button size="small" icon="pi pi-trash" @click="hapusData(data.index, data.nama_kk)" iconPos="right" severity="danger" outlined/>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </template>
    </TemplateLayout>
</template>

<style scoped>
</style>
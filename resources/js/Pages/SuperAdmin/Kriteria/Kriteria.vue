<script setup>
import { onMounted,ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
// import layout
import Layout from '@/Layouts/TemplateLayout.vue'

// import component
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Select from 'primevue/select'
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast"

import kriteriaComp from './Component/KriteriaPage.vue'
import subKriteriaComp from './Component/SubKriteriaPage.vue'

onMounted(() =>
{
    checkNotif()
})

const props = defineProps({ dataKriteria : Object, dataSubKriteria : Object, flash : Object})

const toast = useToast()

let is_kriteria = props.flash?.is_kriteria || ref(true)
const isLoading = ref(false)

let showKriteriaForm = ref(false)
let showSubKriteriaForm = ref(false)

const checkNotif = () =>
{
    if(props.flash.notif_status)
    {
        setTimeout(() =>
        {
            if(props.flash.notif_status === 'success') {
                toast.add({ severity: 'success', summary: 'success', detail: props.flash.notif_message, life: 4000,  group : 'tc' });
            }
            else{
                toast.add({ severity: 'error', summary: 'Info', detail: props.flash.notif_message, life: 4000,  group : 'tc' });
            }
        },1000)
    }
}

const refreshPage = () =>
{
    checkNotif()
    showKriteriaForm.value = false
    showSubKriteriaForm.value = false
    isLoading.value = true
    router.visit(route('super_admin.kriteria'))
    setTimeout(() => isLoading.value = false, 600)
}

const tambahData = () =>
{
    is_kriteria.value ? showKriteriaForm.value = true : showSubKriteriaForm.value = true
}

const kriteriaForm = useForm({
    nama : '',
    nilai_bobot : null
})

const subKriteriaForm = useForm({
    jenis : null,
    nama : null,
    nilai_bobot : null,
    id_relasi : null,
})

const tambahKriteria = () => 
{
    showKriteriaForm.value = false

    kriteriaForm.post(route('super_admin.tambah.kriteria'), 
    {
        onSuccess : () => refreshPage(),
        onError : () => {
            toast.add({ severity: 'error', summary: 'notifikasi', detail:'Gagal menambahkan data kriteria :( ', life: 4000,  group : 'tc' }),
            showKriteriaForm.value = true
        }
    })
}
const tambahSubKriteria = () => 
{
    showSubKriteriaForm.value = false
    subKriteriaForm.post(route('super_admin.tambah.sub_kriteria'), 
    {
        onSuccess : () => refreshPage(),
        onError : () => {
            toast.add({ severity: 'error', summary: 'notifikasi', detail:'Gagal menambahkan data subkrtieria :( ', life: 4000,  group : 'tc' }),
            showSubKriteriaForm.value = true
        }
    })
}

const errorToast = (errorMessage) => 
{
    toast.add({ severity: 'error', summary: 'notifikasi', detail: errorMessage, life: 4000,  group : 'tc' })
}
</script>

<template>
    <Head :title="is_kriteria ? 'Kriteria' : 'Sub Kriteria'" />
    <Layout>
        <template #pageContent>
            <Toast position="top-center" group="tc"/>
            <div class="flex flex-col gap-[1rem] p-4">
                <h1 class="text-lg">Data Kriteria</h1>
                <div class="flex items-center justify-between">
                    <div class="flex gap-8">
                        <Button @click="is_kriteria = true" label="Kriteria" severity="help" size="small" class="w-[12rem]" :outlined="!is_kriteria"/>
                        <Button @click="is_kriteria = false" label="Sub Kriteria" severity="help" size="small" class="w-[12rem]" :outlined="is_kriteria"/>
                    </div>

                    <Button @click="tambahData()" severity="success" label="Tambah Data" size="small" class="w-[12rem]"/>
                </div>
                <!-- dialog tambah data kriteria -->
                <Dialog modal  header="Tambah Kriteria" :style="{width: '40rem'}" v-model:visible="showKriteriaForm">
                    <form @submit.prevent="tambahKriteria" class="flex items-center gap-x-[4rem] flex-wrap" autocomplete="off">
                        <!-- data form -->
                        
                        <div class="flex flex-col gap-4 my-4">
                            <label for="nama" class="font-semibold w-40">Nama Kriteria</label>
                            <InputText v-model="kriteriaForm.nama" id="nama" class="flex-auto" autocomplete="off" placeholder="Masukkan nama kriteria" :invalid="kriteriaForm.errors.nama?true:false" />
                            <span class="text-sm text-red-500" v-if="kriteriaForm.errors.nama">
                                {{ kriteriaForm.errors.nama }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-4 my-4">
                            <label for="nilai" class="font-semibold w-40">Nilai Bobot (%)</label>
                            <InputNumber v-model="kriteriaForm.nilai_bobot" inputId="nilai" mode="decimal" showButtons :min="0" :max="100" placeholder="Masukkan nilai bobot" :invalid="kriteriaForm.errors.nilai_bobot?true:false"/>
                            <span class="text-sm text-red-500" v-if="kriteriaForm.errors.nilai_bobot">
                                {{ kriteriaForm.errors.nilai_bobot }}
                            </span>
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button type="button" label="Reset" severity="danger" @click="kriteriaForm.reset()"/>
                            <Button type="submit" label="Simpan Data" severity="info"/>
                        </div>
                    </form>
                </Dialog>
                <!-- dialog tambah data kriteria selesai -->

                <!-- dialog tambah data subkriteria -->
                <Dialog modal header="Tambah Sub Kriteria" :style="{width: '40rem'}" v-model:visible="showSubKriteriaForm">
                    <form @submit.prevent="tambahSubKriteria" class="flex items-center flex-wrap gap-4" autocomplete="off">
                        <!-- data form -->
                        <div class="flex flex-col w-full gap-4 my-4">
                            <label for="nama" class="font-semibold w-40">Nama Kriteria</label>
                            <InputText v-model="subKriteriaForm.nama" :invalid="subKriteriaForm.errors.nama?true:false" id="nama" class="flex-auto" autocomplete="off" placeholder="Masukkan nama kriteria" />
                            <span class="text-sm text-red-500" v-if="subKriteriaForm.errors.nama">
                                {{ subKriteriaForm.errors.nama }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-4 my-4">
                            <label for="nilai" class="font-semibold w-40">Nilai</label>
                            <InputNumber v-model="subKriteriaForm.nilai_bobot" :invalid="subKriteriaForm.errors.nilai_bobot?true:false" inputId="nilai" mode="decimal" showButtons :min="0" :max="100" placeholder="Masukkan nilai bobot"/>
                            <span class="text-sm text-red-500" v-if="subKriteriaForm.errors.nilai_bobot">
                                {{ subKriteriaForm.errors.nilai_bobot }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-4 my-4">
                            <label for="id_relasi" class="font-semibold w-40">Id Kriteria</label>
                            <Select v-model="subKriteriaForm.id_relasi" :invalid="subKriteriaForm.errors.id_relasi?true:false" :options="props.dataKriteria" optionLabel="jenis" optionValue="id" placeholder="Pilih Id Kriteria"/>
                            <span class="text-sm text-red-500" v-if="subKriteriaForm.errors.id_relasi">
                                {{ subKriteriaForm.errors.id_relasi }}
                            </span>
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button type="button" label="Reset" severity="danger" @click="subKriteriaForm.reset()"/>
                            <Button type="submit" label="Simpan Data" severity="info"/>
                        </div>
                    </form>
                </Dialog>
                <!-- dialog tambah data subkriteria selesai -->
                <kriteriaComp v-if="is_kriteria" :dataKriteria="props.dataKriteria" @refresh-page="refreshPage()"/>
                <subKriteriaComp v-else :dataKriteria="props.dataKriteria" :dataSubKriteria="dataSubKriteria" @refresh-page="refreshPage()" @error-toast="errorToast('Gagal update data sub kriteria :( ')"/>
            </div>
        </template>
    </Layout>
</template>

<style scoped>
</style>
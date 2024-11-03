<script setup>
import { onMounted,ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
// import layout
import Layout from '@/Layouts/SuperAdminLayout.vue'

// import component
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Select from 'primevue/select'
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast"
// import { defineAsyncComponent } from 'vue'

import kriteriaComp from './Component/KriteriaPage.vue'
import subKriteriaComp from './Component/SubKriteriaPage.vue'

// const subKriteriaComp = defineAsyncComponent(() =>
// {
//     import('./Component/SubKriteriaPage.vue')
// })

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
                toast.add({ severity: 'success', summary: 'Info', detail: props.flash.notif_message, life: 4000,  group : 'tc' });
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
    jenis : null,
    nama : null,
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
    kriteriaForm.post(route('super_admin.tambah.kriteria'), 
    {
        onSuccess : () => refreshPage()  
    })
}
const tambahSubKriteria = () => 
{
    subKriteriaForm.post(route('super_admin.tambah.sub_kriteria'), 
    {
        onSuccess : () => refreshPage()  
    })
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
                <Dialog modal header="Tambah Kriteria" :style="{width: '40rem'}" v-model:visible="showKriteriaForm">
                    <form @submit.prevent="tambahKriteria" class="flex flex-col gap-y-2" autocomplete="off">
                        <!-- data form -->
                        <div class="flex items-center gap-4 my-4">
                            <label for="jenis" class="font-semibold w-40">Jenis</label>
                            <InputText v-model="kriteriaForm.jenis" id="jenis" class="flex-auto" autocomplete="off" placeholder="Masukkan jenis" />
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="nama" class="font-semibold w-40">Nama Kriteria</label>
                            <InputText v-model="kriteriaForm.nama" id="nama" class="flex-auto" autocomplete="off" placeholder="Masukkan nama kriteria" />
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="nilai" class="font-semibold w-40">Nilai Bobot (%)</label>
                            <!-- <InputText v-model="kriteriaForm.nilai_bobot" id="nilai" class="flex-auto" autocomplete="off" placeholder="Masukkan nilai bobot" /> -->
                            <InputNumber v-model="kriteriaForm.nilai_bobot" inputId="nilai" mode="decimal" showButtons :min="0" :max="100" placeholder="Masukkan nilai bobot"/>
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button type="button" label="Reset" outlined severity="danger" @click="kriteriaForm.reset()"/>
                            <Button type="submit" label="Simpan Data" outlined severity="info"/>
                        </div>
                    </form>
                </Dialog>
                <!-- dialog tambah data kriteria selesai -->
                <!-- dialog tambah data subkriteria -->
                <Dialog modal header="Tambah Sub Kriteria" :style="{width: '40rem'}" v-model:visible="showSubKriteriaForm">
                    <form @submit.prevent="tambahSubKriteria" class="flex flex-col gap-y-2" autocomplete="off">
                        <!-- data form -->
                        <div class="flex items-center gap-4 my-4">
                            <label for="jenis" class="font-semibold w-40">Id Subkriteria</label>
                            <InputText v-model="subKriteriaForm.jenis" id="jenis" class="flex-auto" autocomplete="off" placeholder="Masukkan jenis" />
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="nama" class="font-semibold w-40">Nama Kriteria</label>
                            <InputText v-model="subKriteriaForm.nama" id="nama" class="flex-auto" autocomplete="off" placeholder="Masukkan nama kriteria" />
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="nilai" class="font-semibold w-40">Nilai</label>
                            <InputNumber v-model="subKriteriaForm.nilai_bobot" inputId="nilai" mode="decimal" showButtons :min="0" :max="100" placeholder="Masukkan nilai bobot"/>
                        </div>

                        <div class="flex items-center gap-4 my-4">
                            <label for="id_relasi" class="font-semibold w-40">Id Kriteria</label>
                            <Select v-model="subKriteriaForm.id_relasi" :options="props.dataKriteria" optionLabel="jenis" optionValue="id" placeholder="Pilih Id Kriteria"/>
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button type="button" label="Reset" outlined severity="danger" @click="subKriteriaForm.reset()"/>
                            <Button type="submit" label="Simpan Data" outlined severity="info"/>
                        </div>
                    </form>
                </Dialog>
                <!-- dialog tambah data subkriteria selesai -->
                <kriteriaComp v-if="is_kriteria" :dataKriteria="props.dataKriteria" @refresh-page="refreshPage()"/>
                <subKriteriaComp v-else :dataKriteria="props.dataKriteria" :dataSubKriteria="dataSubKriteria" @refresh-page="refreshPage()"/>
            </div>
        </template>
    </Layout>
</template>

<style scoped>
</style>
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

// import { defineAsyncComponent } from 'vue'

import kriteriaComp from './Component/KriteriaPage.vue'
import subKriteriaComp from './Component/SubKriteriaPage.vue'

// const subKriteriaComp = defineAsyncComponent(() =>
// {
//     import('./Component/SubKriteriaPage.vue')
// })

const props = defineProps({ dataKriteria : Object, dataSubKriteria : Object})

let is_kriteria = ref(true)
const isLoading = ref(false)

let showKriteriaForm = ref(false)
let showSubKriteriaForm = ref(false)

const refreshPage = () =>
{
    showKriteriaForm.value = false
    showSubKriteriaForm.value = false
    isLoading.value = true
    router.visit(route('super_admin.pengguna'))
    kriteriaForm.reset()
    setTimeout(() => isLoading.value = false, 600)
}

const tambahData = () =>
{
    is_kriteria ? showKriteriaForm.value = true : showSubKriteriaForm.value = true
}

const kriteriaForm = useForm({
    jenis : null,
    nama : null,
    nilai_bobot : null
})

const tambahKriteria = () => 
{
    kriteriaForm.post(route('super_admin.tambah.kriteria'), 
    {
        onSuccess : () => refreshPage()  
    })
}
</script>

<template>
    <Head :title="is_kriteria ? 'Kriteria' : 'Sub Kriteria'" />
    <Layout>
        <template #pageContent>
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
                <!-- dialog tambah data subkriteria selesai -->
                <kriteriaComp v-if="is_kriteria" :dataKriteria="props.dataKriteria"/>
                <subKriteriaComp v-else />
            </div>
        </template>
    </Layout>
</template>

<style scoped>
</style>
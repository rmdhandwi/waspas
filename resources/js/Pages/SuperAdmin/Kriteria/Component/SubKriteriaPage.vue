<script setup>
import { onMounted, ref } from 'vue'
// import component
import Card from 'primevue/card'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Message from 'primevue/message'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from "primevue/useconfirm"
import { useForm } from '@inertiajs/vue3'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Select from 'primevue/select'
import InputIcon from 'primevue/inputicon'
import IconField from 'primevue/iconfield'
import {FilterMatchMode} from '@primevue/core/api' 

const props = defineProps({dataKriteria : Object, dataSubKriteria : Object})

onMounted(() =>
{
    dataSubKriteriaFix.value = props.dataSubKriteria.map((p,i) => ({index : i+1, ...p}))
})

const emit = defineEmits(['refreshPage'])

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const exportCSV = () =>
{
    dt.value.exportCSV()
}

let dt = ref()

let dataSubKriteriaFix = ref([])

const confirm = useConfirm()

const showUbahData = ref(false)

let hapusForm = useForm({
    id : null,
    jenis : null
})

let ubahForm = useForm({
    id : null,
    jenis_sub : null,
    nama_sub : null,
    nilai_bobot : null,
    jenis : null,
    id_relasi : null,
})

const lihatData = (index) =>
{
    showUbahData.value = true
    ubahForm.id = dataSubKriteriaFix.value[index-1]['id']
    ubahForm.jenis_sub = dataSubKriteriaFix.value[index-1]['jenis_sub']
    ubahForm.nama_sub = dataSubKriteriaFix.value[index-1]['nama_sub']
    ubahForm.nilai_bobot = dataSubKriteriaFix.value[index-1]['nilai_bobot']
    ubahForm.jenis = dataSubKriteriaFix.value[index-1]['jenis']
}

const updateData = (id) => 
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
            label: 'Ya',
            severity: 'info'
        },
        accept: () => {
            ubahForm.post(`/super_admin/SubKriteria/update/${id}`, 
            {
                onSuccess : () => emit('refreshPage')
            }
            )
        },

    })
}

const hapusData = (id, jenis) =>
{

    hapusForm.id = id
    hapusForm.jenis = jenis

    confirm.require({
        message: `Yakin ingin menghapus data kriteria : ${jenis} ?`,
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
            hapusForm.post(`/super_admin/SubKriteria/hapus/${id}`, 
            {
                onSuccess : () => emit('refreshPage')
            }
            )
        },

    })
}

</script>

<template>
    <!-- Dialog ubah data subkriteria -->
     <Dialog modal header="Ubah Data Sub Kriteria" :style="{width : '40rem'}" v-model:visible="showUbahData">
        <form @submit.prevent="updateData(ubahForm.id)" class="flex flex-col gap-y-2" autocomplete="off">
            <div class="flex items-center gap-4 my-4">
                <label for="jenis" class="font-semibold w-40">Id Subkriteria</label>
                <InputText v-model="ubahForm.jenis_sub" id="jenis" class="flex-auto" autocomplete="off" placeholder="Masukkan jenis" />
            </div>

            <div class="flex items-center gap-4 my-4">
                <label for="nama" class="font-semibold w-40">Nama Kriteria</label>
                <InputText v-model="ubahForm.nama_sub" id="nama" class="flex-auto" autocomplete="off" placeholder="Masukkan nama kriteria" />
            </div>

            <div class="flex items-center gap-4 my-4">
                <label for="nilai" class="font-semibold w-40">Nilai</label>
                <InputNumber v-model="ubahForm.nilai_bobot" inputId="nilai" mode="decimal" showButtons :min="0" :max="100" placeholder="Masukkan nilai bobot"/>
            </div>

            <div class="flex items-center gap-4 my-4">
                <label for="id_relasi" class="font-semibold w-40">Id Kriteria</label>
                <Select v-model="ubahForm.id_relasi" :options="props.dataKriteria" optionLabel="jenis" optionValue="id" :placeholder="ubahForm.jenis"/>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" label="Batal" outlined severity="danger" @click="showUbahData = false"/>
                <Button type="submit" label="Simpan Data" outlined severity="info"/>
            </div>
        </form>
     </Dialog>
    <!-- Dialog ubah data subkriteria selesai-->
    <ConfirmDialog style="width: 24rem;"/>
    <Card>
        <template #content>
            <DataTable v-model:filters="filters" ref="dt" :value="dataSubKriteriaFix" paginator :rows="10">
                <template #header>
                    <div class="flex items-center justify-between">
                        <Button icon="pi pi-external-link" label="Export" @click="exportCSV($event)" size="small"/>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search me-4" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Cari Data Sub Kriteria" />
                        </IconField>
                    </div>
                </template>
                <template #empty>
                    <span class="flex justify-center items-center text-lg">Tidak ada data</span>
                </template>
                <Column sortable field="index" header="No"/>
                <Column sortable field="jenis_sub" header="Id Sub Kriteria"/>
                <Column sortable field="nama_sub" header="Nama Kriteria"/>
                <Column sortable field="nilai_bobot" header="Nilai Bobot"/>
                <Column sortable field="jenis" header="Id Kriteria"/>
                <Column header="Opsi">
                    <template #body="{data}">
                        <div class="flex gap-2 items-center">
                            <Button size="small" @click="lihatData(data.index)" icon="pi pi-pen-to-square" iconPos="right" severity="info" outlined/>
                            <Button size="small" @click="hapusData(data.id, data.jenis)" icon="pi pi-trash" iconPos="right" severity="danger" outlined/>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>
</template>

<style scoped>
</style>
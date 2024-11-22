<script setup>
import { onMounted, ref } from 'vue'
// import component
import Card from 'primevue/card'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import NavLink from '@/Components/NavLink.vue'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from "primevue/useconfirm"
import { useForm } from '@inertiajs/vue3'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import InputIcon from 'primevue/inputicon'
import IconField from 'primevue/iconfield'
import {FilterMatchMode} from '@primevue/core/api' 


const props = defineProps({dataKriteria : Object})

onMounted(() =>
{
    dataFix.value = props.dataKriteria.map((p,i) => ({index : i+1, persen :  p.nilai_bobot + '%',...p}))
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

let dataFix = ref([])

let showUbahForm = ref(false)

const confirm = useConfirm()

let hapusForm = useForm({
    id : null,
    jenis : null
})

let kriteriaForm = useForm({
    id : null,
    jenis : null,
    nama : null,
    nilai_bobot : null,
})

const lihatData = (index) =>
{
    showUbahForm.value = true
    kriteriaForm.id = dataFix.value[index-1]['id']
    kriteriaForm.jenis = dataFix.value[index-1]['jenis']
    kriteriaForm.nama = dataFix.value[index-1]['nama']
    kriteriaForm.nilai_bobot = dataFix.value[index-1]['nilai_bobot']
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
            hapusForm.post(`/super_admin/Kriteria/hapus/${id}`, 
            {
                onSuccess : () => emit('refreshPage')
            }
            )
        },

    })
}

const updateData = (id) => 
{
    confirm.require({
        message: `Simpan Data ?`,
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
            showUbahForm.value = false
            kriteriaForm.post(`/super_admin/Kriteria/update/${id}`, 
            {
                onSuccess : () => emit('refreshPage')
            }
            )
        },

    })
}

</script>

<template>
    <!-- Dialog ubah data kriteria -->
     <Dialog modal header="Ubah Data Kriteria" :style="{width : '40rem'}" v-model:visible="showUbahForm">
         <form @submit.prevent="updateData(kriteriaForm.id)" class="flex flex-col gap-y-2" autocomplete="off">
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
                 <InputNumber v-model="kriteriaForm.nilai_bobot" inputId="nilai" mode="decimal" showButtons :min="0" :max="100" placeholder="Masukkan nilai bobot"/>
             </div>
     
             <div class="flex justify-end gap-2">
                 <Button type="button" label="Batal" outlined severity="danger" @click="showUbahForm = false, kriteriaForm.reset()"/>
                 <Button type="submit" label="Simpan Data" outlined severity="info"/>
             </div>
         </form>
     </Dialog>
    <!-- Dialog ubah data kriteria selesai -->
    <ConfirmDialog style="width: 24rem;"/>
    <Card>
        <template #content>
            <DataTable v-model:filters="filters" ref="dt" :value="dataFix" paginator :rows="10">
                <template #header>
                    <div class="flex items-center justify-between">
                        <Button icon="pi pi-external-link" label="Export" @click="exportCSV($event)" size="small"/>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search me-4" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Cari Data Kriteria" />
                        </IconField>
                    </div>
                </template>
                <template #empty>
                    <span class="flex justify-center items-center text-lg">Tidak ada data</span>
                </template>
                <Column sortable field="index" header="No"/>
                <Column sortable field="jenis" header="Id Kriteria"/>
                <Column sortable field="nama" header="Nama Kriteria"/>
                <Column sortable field="persen" header="Nilai Bobot"/>
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
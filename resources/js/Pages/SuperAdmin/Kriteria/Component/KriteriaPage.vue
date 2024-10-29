<script setup>
import { onMounted, ref } from 'vue';
// import component
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import NavLink from '@/Components/NavLink.vue';
const props = defineProps({dataKriteria : Object})

onMounted(() =>
{
    dataFix.value = props.dataKriteria.map((p,i) => ({index : i+1, persen :  p.nilai_bobot + '%',...p}))
})

let dataFix = ref([])

</script>

<template>
    <Card>
        <template #content>
            <DataTable :value="dataFix" paginator :rows="10">
                <Column sortable field="index" header="No"/>
                <Column sortable field="jenis" header="Id Kriteria"/>
                <Column sortable field="nama" header="Nama Kriteria"/>
                <Column sortable field="persen" header="Nilai Bobot"/>
                <Column header="Opsi">
                    <template #body="{data}">
                        <div class="flex gap-2 items-center">
                            <NavLink class="border-none p-0 m-0" :href="`Kriteria/view/${data.id}`">
                                <Button size="small" icon="pi pi-pen-to-square" iconPos="right" severity="info" outlined />
                            </NavLink>
                            <Button size="small" icon="pi pi-trash" iconPos="right" severity="danger" outlined/>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>
</template>

<style scoped>
</style>
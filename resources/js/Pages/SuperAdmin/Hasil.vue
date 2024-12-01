<script setup>
import { onMounted, ref } from "vue";
import { Head } from "@inertiajs/vue3";
// import layout
import Layout from "@/Layouts/TemplateLayout.vue";

// import component
import {
    Button,
    Card,
    DataTable,
    IconField,
    InputIcon,
    InputText,
    Column,
} from "primevue";

import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    hasil: Object,
    auth: Object,
    flash: Object,
});

let dataHasil = ref([]);

const exportCSV = () => {
    dt.value.exportCSV();
};

let dt = ref();

onMounted(() => {
    dataHasil.value = props.hasil.map((p, i) => ({
        index: i + 1,
        ...p,
    }));
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Format nama kolom untuk keterbacaan yang lebih baik
const formatName = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};
</script>

<template>
    <Head title="Periode" />
    <Layout :auth="auth">
        <template #pageContent>
            <div class="flex flex-col gap-4 p-4">
                <h1 class="text-xl font-semibold">Data Hasil Akhir</h1>
            </div>
            <Card>
                <template #content>
                    <DataTable
                        removableSort
                        v-model:filters="filters"
                        ref="dt"
                        :value="dataHasil"
                        paginator
                        :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                        :rows="10"
                    >
                        <template #header>
                            <div class="flex items-center justify-between">
                                <Button
                                    icon="pi pi-external-link"
                                    label="Export"
                                    @click="exportCSV($event)"
                                    size="small"
                                />
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText
                                        v-model="filters['global'].value"
                                        placeholder="Cari Data Periode"
                                    />
                                </IconField>
                            </div>
                        </template>
                        <template #empty>
                            <span
                                class="flex justify-center items-center text-lg"
                            >
                                Tidak ada data
                            </span>
                        </template>
                        <Column sortable field="index" header="No" />
                        <Column sortable header="Nama Warga">
                            <template #body="{ data }">
                                {{ formatName(data.warga.nama_kk) }}
                            </template>
                        </Column>
                        <Column
                            sortable
                            field="skor_akhir"
                            header="Hasil Akhir"
                        />
                        <Column sortable field="peringkat" header="Peringkat" />
                    </DataTable>
                </template>
            </Card>
        </template>
    </Layout>
</template>

<style scoped></style>

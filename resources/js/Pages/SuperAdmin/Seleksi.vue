<script setup>
import { defineProps, ref, watch, onMounted } from "vue";
import { Head, router } from "@inertiajs/vue3";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import {
    DataTable,
    Column,
    Card,
    InputNumber,
    Button,
    FloatLabel,
} from "primevue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    result: Array, // Data hasil normalisasi dan alternatif
    periode: Array,
    kriteria: Array, // Data kriteria yang diterima untuk header tabel
    auth: Object, // Data autentikasi pengguna
});

const dt = ref(null); // Referensi untuk DataTable
const exportCount = ref(null); // Default jumlah data yang ingin disimpan
const filteredResult = ref([]);

// Set default exportCount ke jumlah result pada mounted
onMounted(() => {
    exportCount.value = props.result.length; // Set default exportCount ke jumlah hasil yang ada
});

// Watch untuk memfilter hasil berdasarkan `exportCount`
watch(
    () => exportCount.value,
    (newCount) => {
        // Ambil hasil berdasarkan jumlah `exportCount`
        filteredResult.value = props.result.slice(0, newCount);
    },
    { immediate: true }
);

// Fungsi untuk mengekspor data ke CSV
const exportCSV = () => {
    dt.value.exportCSV();
};

// Inertia form untuk mengirim data ke backend
const form = useForm({
    results: [], // Data yang akan disimpan
    export_count: exportCount.value, // Jumlah data yang dipilih
});

// Fungsi untuk menyimpan data
const saveData = () => {
    form.results = filteredResult.value; // Ambil hasil yang difilter
    form.post(route("simpanDataHasil"), {
        onSuccess: () => {
            form.reset("results");
        },
    });
};

// Format nama kolom untuk keterbacaan
const formatName = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};
</script>

<template>
    <Head title="Seleksi" />
    <TemplateLayout :auth="auth">
        <template #pageContent>
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-lg font-bold">Data Seleksi</h1>
                    <p v-if="periode.length">Periode: {{ periode[0].tahun }}</p>
                    <p v-else class="text-red-500">Periode tidak ditemukan!</p>
                </div>

                <!-- Input untuk jumlah data -->
                <form @submit.prevent="saveData">
                    <div class="flex items-center justify-between mt-3">
                        <FloatLabel variant="on">
                            <InputNumber
                                id="exportCount"
                                v-model="form.export_count"
                                :min="1"
                                :max="props.result.length"
                                :step="1"
                                size="small"
                            />
                            <label for="exportCount">Jumlah Data</label>
                        </FloatLabel>

                        <div>
                            <Button
                                label="Ekspor CSV"
                                icon="pi pi-file"
                                size="small"
                                class="me-2"
                                @click="exportCSV"
                            />
                            <Button
                                label="Simpan"
                                icon="pi pi-download"
                                size="small"
                                class="display-inline"
                                type="submit"
                            />
                        </div>
                    </div>
                </form>

                <!-- Tabel Normalisasi -->
                <Card class="mt-2 mb-5">
                    <template #title>
                        <h2>Normalisasi</h2>
                    </template>
                    <template #content>
                        <DataTable
                            :value="filteredResult"
                            paginator
                            :rows="10"
                            showGridlines
                            resizableColumns
                            columnResizeMode="fit"
                            responsiveLayout="scroll"
                            :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                            ref="dt"
                        >
                            <Column header="Alternatif">
                                <template #body="{ data }">
                                    {{ formatName(data.alternatif) }}
                                </template>
                            </Column>
                            <Column
                                v-for="(kriteriaItem, index) in kriteria"
                                :key="index"
                                :field="
                                    'normalisasi.' + kriteriaItem.kode_kriteria
                                "
                                :header="kriteriaItem.kode_kriteria"
                                :body="
                                    (data) =>
                                        data.normalisasi[
                                            kriteriaItem.kode_kriteria
                                        ]?.toFixed(3) || '-'
                                "
                            />
                        </DataTable>
                    </template>
                </Card>

                <!-- Tabel Hasil Akhir -->
                <Card>
                    <template #title>
                        <h2>Hasil Akhir</h2>
                    </template>
                    <template #content>
                        <DataTable
                            :value="filteredResult"
                            paginator
                            ref="dt"
                            showGridlines
                            size="null"
                            resizableColumns
                            columnResizeMode="fit"
                            :rows="10"
                            :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                            :responsiveLayout="'scroll'"
                        >
                            <Column header="Alternatif">
                                <template #body="{ data }">
                                    {{ formatName(data.alternatif) }}
                                </template>
                            </Column>
                            <Column field="qi" header="Nilai Qi" />
                            <Column field="penghasilan" header="Penghasilan">
                                <template #body="{ data }">
                                    {{ formatName(data.penghasilan) }}
                                </template>
                            </Column>
                            <Column
                                field="jumlah_penghuni"
                                header="Jumlah Anggota keluarga"
                            >
                                <template #body="{ data }">
                                    {{ formatName(data.jumlah_penghuni) }}
                                </template>
                            </Column>
                            <Column
                                field="status_kepemilikan_rumah"
                                header="Kepemilikan Rumah"
                            >
                                <template #body="{ data }">
                                    {{ formatName(data.status_kepemilikan_rumah) }}
                                </template>
                            </Column>
                            <Column field="ranking" header="Ranking" />
                        </DataTable>
                    </template>
                </Card>
            </div>
        </template>
    </TemplateLayout>
</template>

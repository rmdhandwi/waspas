<script setup>
import { onMounted, ref, computed } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";

// Import PrimeVue components
import {
    Button,
    Card,
    DataTable,
    IconField,
    InputIcon,
    InputText,
    Column,
    FloatLabel,
    Select,
    Toast,
<<<<<<< HEAD
    Tag,
=======
>>>>>>> dc10fea88a9c5e2d0d33b4c13a67e85adaa908c4
    useToast,
} from "primevue";

import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
<<<<<<< HEAD
    perhitungan: Object,
    total: Number,
=======
    hasil: Object,
>>>>>>> dc10fea88a9c5e2d0d33b4c13a67e85adaa908c4
    flash: Object,
    periode: Object,
});

const dt = ref();

let dataHasil = ref([]);
const toast = useToast(); // Inisialisasi Toast

// Fungsi untuk memeriksa dan menampilkan notifikasi flash
const ShowToast = () => {
    if (props.flash && props.flash.notif_status) {
        toast.add({
            severity: props.flash.notif_status || "info", // Default 'info'
            summary:
                props.flash.notif_status.charAt(0).toUpperCase() +
                props.flash.notif_status.slice(1),
            detail: props.flash.notif_message,
            life: 4000,
            group: "tc",
        });
    }
};

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Inisialisasi data hasil
onMounted(() => {
<<<<<<< HEAD
    dataHasil.value = props.perhitungan.map((p, i) => ({
=======
    dataHasil.value = props.hasil.map((p, i) => ({
>>>>>>> dc10fea88a9c5e2d0d33b4c13a67e85adaa908c4
        index: i + 1,
        ...p,
    }));
    ShowToast(); // Cek notifikasi saat komponen dimuat
});

const view = useForm({
    periode: null,
});

<<<<<<< HEAD
// Filter data berdasarkan dropdown dan hitung total warga dengan status = 1
const filteredDataHasil = computed(() => {
    // Filter data berdasarkan periode yang dipilih
    const filteredData = dataHasil.value.filter(
        (item) => item.warga.periode?.id === view.periode
    );

    // Hitung total warga dengan status = 1
    const total = filteredData.reduce(
        (count, item) => (item.warga.status === 1 ? count + 1 : count),
        0
    );

    // Return data yang difilter dan total warga dengan status = 1
    return {
        data: filteredData,
        total: total,
    };
=======
// Filter data berdasarkan dropdown
const filteredDataHasil = computed(() => {
    return dataHasil.value.filter(
        (item) => item.warga.periode?.id === view.periode
    );
>>>>>>> dc10fea88a9c5e2d0d33b4c13a67e85adaa908c4
});

// Format nama kolom
const formatName = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};
</script>

<template>
    <Head title="Hasil Seleksi" />
    <Toast position="top-center" group="tc" />

    <div
        class="width-full flex flex-col justify-center items-center px-4 sm:px-8"
    >
        <!-- Logo -->
        <img
            src="logo.png"
            alt="Logo"
            class="h-20 w-auto mb-2 mt-2 drop-shadow-md"
        />

        <Button
            as="a"
            :href="route('login')"
            icon="pi pi-arrow-left"
            severity="danger"
            label="Kembali"
            size="small"
            variant="text"
            class="mb-2"
        />

        <!-- Judul -->
        <h1
            class="text-2xl font-bold text-blue-700 text-center mb-4 drop-shadow-xl"
        >
            DATA HASIL PENERIMA BANTUAN PERBAIKAN RUMAH
        </h1>

        <!-- Kartu Konten -->
        <Card
            class="shadow-lg border border-gray-200 rounded-lg w-full max-w-7xl"
        >
            <template #header>
                <div
                    class="flex flex-wrap items-center justify-between p-4 gap-4"
                >
                    <!-- Dropdown Periode -->
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <div class="w-[250px]">
                            <FloatLabel variant="on">
                                <Select
                                    fluid
                                    v-model="view.periode"
                                    :options="props.periode"
                                    optionValue="id"
                                    optionLabel="tahun"
                                    class="drop-shadow-md"
                                />
                                <label>Periode</label>
                            </FloatLabel>
                        </div>
                    </div>

                    <!-- Input Cari -->
                    <div class="w-full sm:w-auto drop-shadow-md">
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search me-4 text-gray-500" />
                            </InputIcon>
                            <InputText
                                v-model="filters['global'].value"
                                placeholder="Cari Data Warga"
                                class="w-full sm:w-auto"
                            />
                        </IconField>
                    </div>
                </div>
<<<<<<< HEAD
                <Tag
                    class="ms-4"
                    icon="pi pi-clipboard"
                    severity="info"
                    :value="'Total Kouta Bantuan : ' + filteredDataHasil.total"
                />
=======
>>>>>>> dc10fea88a9c5e2d0d33b4c13a67e85adaa908c4
            </template>

            <template #content>
                <DataTable
                    removableSort
                    v-model:filters="filters"
                    ref="dt"
<<<<<<< HEAD
                    :value="filteredDataHasil.data"
=======
                    :value="filteredDataHasil"
>>>>>>> dc10fea88a9c5e2d0d33b4c13a67e85adaa908c4
                    stripedRows
                    paginator
                    scrollable
                    resizableColumns
                    columnResizeMode="fit"
                    size="large"
                    :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                    :rows="10"
                    class="shadow-md rounded-md overflow-hidden"
                >
                    <template #empty>
                        <span
                            class="flex justify-center items-center text-lg text-gray-500"
                        >
                            Tidak ada data
                        </span>
                    </template>
                    <Column sortable field="index" header="No" />
                    <Column
                        sortable
                        field="warga.periode.tahun"
                        header="Periode"
                    />
                    <Column sortable header="Nama Warga" field="warga.nama_kk">
                        <template #body="{ data }">
                            {{ formatName(data.warga.nama_kk) }}
                        </template>
                    </Column>
                    <Column sortable header="Hasil Akhir" field="skor_akhir" />
                    <Column sortable header="Rank" field="peringkat" />
<<<<<<< HEAD
                    <Column sortable header="Status Layak" field="status">
                        <template #body="{ data }">
                            <Tag
                                v-if="data.status == 'Layak'"
                                severity="success"
                                :value="data.status"
                                icon="pi pi-check-circle"
                            />
                            <Tag
                                v-else
                                icon="pi pi-times"
                                severity="danger"
                                :value="data.status"
                            />
                        </template>
                    </Column>
                    <Column
                        sortable
                        header="Status Menerima"
                        field="warga.status"
                    >
                        <template #body="{ data }">
                            <Tag
                                v-if="data.warga.status == 1"
                                severity="success"
                                value="Telah Menerima Bantuan"
                                icon="pi pi-check-circle"
                            />
                            <Tag
                                v-else
                                icon="pi pi-times"
                                severity="danger"
                                value="Belum Menerima Bantuan"
                            />
                        </template>
                    </Column>
=======
>>>>>>> dc10fea88a9c5e2d0d33b4c13a67e85adaa908c4
                </DataTable>
            </template>
        </Card>
    </div>
</template>

<style scoped>
/* Tambahkan beberapa gaya untuk mempercantik */
.card {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

h1 {
    font-family: "Poppins", sans-serif;
}
</style>

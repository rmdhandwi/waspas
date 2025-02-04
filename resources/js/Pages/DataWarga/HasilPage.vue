<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Head } from "@inertiajs/vue3";
import {
    Button,
    Card,
    DataTable,
    Column,
    FloatLabel,
    Select,
    Tag,
    Toast,
    useToast,
    Tabs,
    TabList,
    TabPanels,
    Tab,
    TabPanel,
    IconField,
    InputIcon,
    InputText,
} from "primevue";
import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    perhitungan: Array,
    periode: Array,
    groupedByHitung: Object,
    flash: Object,
});

const selectedPeriode = ref(null);
const activeTab = ref(null);
const toast = useToast();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const quotaCountByHitung = computed(() => {
    const quotaCounts = {};
    for (const [hitung, group] of Object.entries(filteredDataByPeriode.value)) {
        quotaCounts[hitung] = group.filter(
            (item) => item.terima === "Telah Menerima"
        ).length;
    }
    return quotaCounts;
});

// Filter data berdasarkan periode
const filteredDataByPeriode = computed(() => {
    if (!selectedPeriode.value) return {};
    const filtered = {};
    for (const [key, group] of Object.entries(props.groupedByHitung)) {
        filtered[key] = group.filter(
            (item) => item.warga.periode?.id === selectedPeriode.value
        );
    }
    return filtered;
});

watch(selectedPeriode, (newValue) => {
    if (newValue) {
        const firstTab = Object.keys(filteredDataByPeriode.value)[0] || null;
        activeTab.value = firstTab; // Set tab awal ke tab pertama
    } else {
        activeTab.value = null; // Reset tab jika periode tidak dipilih
    }
});

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

onMounted(() => {
    ShowToast();
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
    <Head title="Hasil Seleksi" />
    <Toast position="top-center" />

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
    </div>

    <div class="p-6 space-y-4">
        <!-- Dropdown Periode -->
        <Card class="shadow-md">
            <template #content>
                <div class="flex items-center justify-between">
                    <div class="w-full sm:w-auto drop-shadow-md flex gap-2">
                        <FloatLabel>
                            <Select
                                v-model="selectedPeriode"
                                :options="props.periode"
                                optionValue="id"
                                optionLabel="tahun"
                                class="w-[300px]"
                            />
                            <label>Pilih Periode</label>
                        </FloatLabel>
                        <Button
                            icon="pi pi-refresh"
                            label="Reset"
                            class="p-button-outlined"
                            @click="selectedPeriode = null"
                        />
                    </div>
                </div>
            </template>
        </Card>

        <!-- Menampilkan Pesan Jika Tidak Ada Data -->
        <div
            v-if="
                !selectedPeriode ||
                Object.keys(filteredDataByPeriode).length === 0
            "
            class="text-center text-red-600 font-semibold"
        >
            Belum ada data untuk periode yang dipilih.
        </div>

        <!-- Tabs untuk Menampilkan Data Berdasarkan Hitung -->
        <Card
            v-if="
                selectedPeriode && Object.keys(filteredDataByPeriode).length > 0
            "
            class="mt-4"
        >
            <template #content>
                <Tabs v-model="activeTab" class="w-full">
                    <!-- Daftar Tab -->
                    <TabList class="flex border-b mb-4">
                        <template
                            v-for="(group, hitung) in filteredDataByPeriode"
                            :key="hitung"
                        >
                            <Tab :value="hitung"
                                >Perhitungan ke : {{ hitung }}</Tab
                            >
                        </template>
                    </TabList>

                    <!-- Konten Tab -->
                    <TabPanels>
                        <template
                            v-for="(group, hitung) in filteredDataByPeriode"
                            :key="hitung"
                        >
                            <TabPanel :value="hitung">
                                <DataTable
                                    removableSort
                                    v-model:filters="filters"
                                    ref="dt"
                                    resizableColumns
                                    columnResizeMode="fit"
                                    size="large"
                                    :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                                    class="shadow-md rounded-md overflow-hidden"
                                    :value="group"
                                    paginator
                                    :rows="10"
                                    stripedRows
                                    scrollable
                                >
                                    <template #header>
                                        <div
                                            class="flex justify-between items-center"
                                        >
                                            <!-- Tag untuk menampilkan jumlah kuota -->
                                            <Tag
                                                icon="pi pi-megaphone"
                                                :value="
                                                    'Jumlah Kuota: ' +
                                                    (quotaCountByHitung[
                                                        hitung
                                                    ] || 0)
                                                "
                                            />

                                            <!-- Input Cari -->
                                            <div
                                                class="w-full sm:w-auto drop-shadow-md"
                                            >
                                                <IconField>
                                                    <InputIcon>
                                                        <i
                                                            class="pi pi-search me-4 text-gray-500"
                                                        />
                                                    </InputIcon>
                                                    <InputText
                                                        v-model="
                                                            filters['global']
                                                                .value
                                                        "
                                                        placeholder="Cari Data Warga"
                                                        class="w-full sm:w-auto"
                                                    />
                                                </IconField>
                                            </div>
                                        </div>
                                    </template>

                                    <template #empty>
                                        <div class="text-center text-gray-500">
                                            Data Warga tidak Ditemukan
                                        </div>
                                    </template>
                                    <!-- Kolom Nomor Urut -->
                                    <Column
                                        header="No"
                                        :field="
                                            group + 1
                                        "
                                    >
                                        <template #body="slotProps">
                                            {{
                                                group.indexOf(slotProps.data) +
                                                1
                                            }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="warga.nama_kk"
                                        header="Nama Warga"
                                    >
                                        <template #body="{ data }">
                                            {{ formatName(data.warga.nama_kk) }}
                                        </template>
                                    </Column>
                                    <Column
                                        field="warga.periode.tahun"
                                        header="Periode"
                                    />
                                    <!-- <Column
                                        field="skor_akhir"
                                        header="Hasil Akhir"
                                        sortable
                                    /> -->
                                    <Column
                                        header="Status Menerima"
                                        field="terima"
                                    >
                                        <template #body="{ data }">
                                            <Tag
                                                v-if="
                                                    data.terima ===
                                                    'Telah Menerima'
                                                "
                                                icon="pi pi-check-circle"
                                                value="Telah Menerima"
                                                severity="success"
                                            />
                                            <Tag
                                                v-else
                                                icon="pi pi-times"
                                                value="Belum Menerima"
                                                severity="danger"
                                            />
                                        </template>
                                    </Column>
                                </DataTable>
                            </TabPanel>
                        </template>
                    </TabPanels>
                </Tabs>
            </template>
        </Card>
    </div>
</template>

<style scoped>
.card {
    padding: 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>

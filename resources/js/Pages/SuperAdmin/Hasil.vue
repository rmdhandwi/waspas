<script setup>
import { onMounted, ref, computed } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";

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
    Dialog,
    FloatLabel,
    Message,
    DatePicker,
    Select,
    Toast,
    useToast,
} from "primevue";

import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    hasil: Object,
    auth: Object,
    flash: Object,
    periode: Object,
});

const dt = ref();
const exportCSV = () => {
    dt.value.exportCSV();
};

let dataHasil = ref([]);
const toast = useToast(); // Inisialisasi Toast

const showForm = ref(false);
const cetakForm = useForm({
    periode: null,
    tgl: null,
    oleh: "",
    setuju: "",
});

// Fungsi untuk memeriksa dan menampilkan notifikasi flash
const ShowToast = () => {
    if (props.flash && props.flash.notif_status) {
        toast.add({
            severity: props.flash.notif_status || "info", // Gunakan 'info' sebagai default jika tidak ada notif
            summary:
                props.flash.notif_status.charAt(0).toUpperCase() +
                props.flash.notif_status.slice(1), // Kapitalisasi pertama
            detail: props.flash.notif_message,
            life: 4000,
            group: "tc",
        });
    }
};

// Menampilkan dialog cetak dan reset formulir
const cetakShow = () => {
    showForm.value = true;
    cetakForm.clearErrors();
    cetakForm.reset();
};

// Fungsi cetak
const cetak = () => {
    cetakForm.post(route("cetakPage"), {
        onSuccess: () => {
            ShowToast();
        },
    });
};

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Inisialisasi data hasil
onMounted(() => {
    dataHasil.value = props.hasil.map((p, i) => ({
        index: i + 1,
        ...p,
    }));
    ShowToast(); // Cek notifikasi saat komponen dimuat
});

// Filter data berdasarkan dropdown
const filteredDataHasil = computed(() => {
    if (!cetakForm.periode) {
        return dataHasil.value; // Jika tidak ada pilihan, tampilkan semua data
    }
    return dataHasil.value.filter(
        (item) => item.warga.periode?.id === cetakForm.periode
    );
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
    <Head title="Periode" />
    <Toast position="top-center" group="tc" />
    <Layout :auth="auth">
        <template #pageContent>
            <div class="flex flex-col gap-4 p-4">
                <h1 class="text-xl font-semibold">Data Hasil Akhir</h1>
            </div>

            <Card>
                <template #header>
                    <div
                        class="flex flex-wrap items-center justify-between p-4 gap-2"
                    >
                        <!-- Dropdown Periode -->
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="w-[250px]">
                                <FloatLabel variant="on">
                                    <Select
                                        fluid
                                        v-model="cetakForm.periode"
                                        :options="props.periode"
                                        optionValue="id"
                                        optionLabel="tahun"
                                    />
                                    <label>Periode</label>
                                </FloatLabel>
                            </div>

                            <!-- Tombol Export dan Cetak -->
                            <div
                                class="flex gap-2"
                                v-if="props.auth.user.role !== 'warga'"
                            >
                                <Button
                                    icon="pi pi-external-link"
                                    label="Export"
                                    @click="exportCSV()"
                                    size="small"
                                    class="w-full sm:w-auto"
                                />
                                <Button
                                    icon="pi pi-print"
                                    label="Cetak"
                                    @click="cetakShow"
                                    size="small"
                                    class="w-full sm:w-auto"
                                    severity="secondary"
                                />
                            </div>
                        </div>

                        <Dialog
                            ref="dt"
                            v-model:visible="showForm"
                            modal
                            header="Form Cetak"
                            :style="{ width: '50vw' }"
                            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                        >
                            <form @submit.prevent="cetak()">
                                <div class="grid grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <FloatLabel variant="on">
                                            <Select
                                                fluid
                                                inputId="tahun"
                                                :options="props.periode"
                                                optionValue="id"
                                                optionLabel="tahun"
                                                v-model="cetakForm.periode"
                                                :invalid="
                                                    !!cetakForm.errors.periode
                                                "
                                            />
                                            <label for="tahun">Periode</label>
                                        </FloatLabel>
                                        <Message
                                            v-if="cetakForm.errors.periode"
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{ cetakForm.errors.periode }}
                                        </Message>
                                    </div>
                                    <div>
                                        <FloatLabel variant="on">
                                            <DatePicker
                                                fluid
                                                v-model="cetakForm.tgl"
                                                inputId="tgl"
                                                showIcon
                                                iconDisplay="input"
                                                :invalid="
                                                    !!cetakForm.errors.tgl
                                                "
                                            />
                                            <label for="tgl">Tanggal</label>
                                        </FloatLabel>
                                        <Message
                                            v-if="cetakForm.errors.tgl"
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{ cetakForm.errors.tgl }}
                                        </Message>
                                    </div>
                                    <div>
                                        <FloatLabel variant="on">
                                            <InputText
                                                inputId="oleh"
                                                fluid
                                                v-model="cetakForm.oleh"
                                                :invalid="
                                                    !!cetakForm.errors.oleh
                                                "
                                            />
                                            <label for="oleh"
                                                >Dibuat Oleh</label
                                            >
                                        </FloatLabel>
                                        <Message
                                            v-if="cetakForm.errors.oleh"
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{ cetakForm.errors.oleh }}
                                        </Message>
                                    </div>
                                    <div>
                                        <FloatLabel variant="on">
                                            <InputText
                                                inputId="setuju"
                                                fluid
                                                v-model="cetakForm.setuju"
                                                :invalid="
                                                    !!cetakForm.errors.setuju
                                                "
                                            />
                                            <label for="setuju"
                                                >Disetujui Oleh</label
                                            >
                                        </FloatLabel>
                                        <Message
                                            v-if="cetakForm.errors.setuju"
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{ cetakForm.errors.setuju }}
                                        </Message>
                                    </div>
                                </div>
                                <div class="flex justify-center mt-4 gap-2">
                                    <Button
                                        label="Cetak"
                                        type="submit"
                                        icon="pi pi-print"
                                        severity="secondary"
                                        variant="outlined"
                                        size="small"
                                    />
                                    <Button
                                        label="Batal"
                                        type="button"
                                        icon="pi pi-times"
                                        size="small"
                                        variant="outlined"
                                        severity="danger"
                                        @click="showForm = false"
                                    />
                                </div>
                            </form>
                        </Dialog>

                        <!-- Input Cari -->
                        <div class="w-full sm:w-auto">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search me-4" />
                                </InputIcon>
                                <InputText
                                    v-model="filters['global'].value"
                                    placeholder="Cari Data Warga"
                                    class="w-full sm:w-auto"
                                />
                            </IconField>
                        </div>
                    </div>
                </template>
                <template #content>
                    <DataTable
                        removableSort
                        v-model:filters="filters"
                        ref="dt"
                        :value="filteredDataHasil"
                        stripedRows
                        paginator
                        scrollable
                        resizableColumns
                        columnResizeMode="fit"
                        size="large"
                        :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                        :rows="10"
                    >
                        <template #empty>
                            <span
                                class="flex justify-center items-center text-lg"
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
                        <Column
                            sortable
                            header="Nama Warga"
                            field="warga.nama_kk"
                        >
                            <template #body="{ data }">
                                {{ formatName(data.warga.nama_kk) }}
                            </template>
                        </Column>
                        <Column
                            sortable
                            header="Hasil Akhir"
                            field="skor_akhir"
                        />
                    </DataTable>
                </template>
            </Card>
        </template>
    </Layout>
</template>

<style scoped></style>

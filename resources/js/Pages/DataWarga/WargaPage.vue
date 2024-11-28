<script setup>
import { onMounted, ref, defineProps } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import {
    Toast,
    useToast,
    Dialog,
    Button,
    InputText,
    Select,
    ConfirmDialog,
    useConfirm,
    DataTable,
    Column,
    IconField,
    InputIcon,
    FloatLabel,
    Message,
} from "primevue";
import { FilterMatchMode } from "@primevue/core/api";

// Props
const pageProps = defineProps({
    periode: Array,
    kriteria: Array,
    subkriteria: Array,
    warga: Array,
    flash: Object,
    auth: Object,
});

const auth = pageProps.auth;

// State
const kriteria = ref([]);
const subkriteria = ref([]);
const Wargadata = ref([]);
const showForm = ref(false);
const isLoading = ref(false);

let optionsAgama = [
    { label: "Islam", value: "islam" },
    { label: "Kristen", value: "kristen" },
    { label: "Katolik", value: "katolik" },
    { label: "Hindu", value: "hindu" },
    { label: "Buddha", value: "buddha" },
    { label: "Konghucu", value: "konghucu" },
];

let optionsJk = [
    { label: "Laki-laki", value: "laki-laki" },
    { label: "Perempuan", value: "perempuan" },
];

// Form
const formWarga = useForm({
    provinsi: "papua",
    kabupaten: "sentani",
    kampung: "sereh",
    nomor_kk: null,
    nama_kk: null,
    periode_id: null,
    rt: null,
    rw: null,
    pekerjaan: null,
    agama: null,
    jenis_kelamin: null,
    asal_suku: null,
    kriteria_values: {},
});

// Filters
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// PrimeVue Utilities
const toast = useToast();
const confirm = useConfirm();
let dt = ref();

// Lifecycle
onMounted(() => {
    initializeData();
    checkNotif();
});

// Initialize Data
const initializeData = () => {
    kriteria.value = pageProps.kriteria;
    subkriteria.value = pageProps.subkriteria;

    // Setup form default values
    kriteria.value.forEach((item) => {
        formWarga.kriteria_values[item.nama_kriteria] = null;
    });

    // Map warga data with index
    Wargadata.value = pageProps.warga.map((warga, index) => ({
        index: index + 1,
        ...warga,
    }));
};

// Format column names for better readability
const formatName = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

// Filter subkriteria by kriteria ID
const getSubkriteria = (kriteriaId) => {
    return subkriteria.value
        .filter((sub) => sub.kriteria_id === kriteriaId)
        .map((sub) => ({
            ...sub,
            nama_subkriteria_formatted: formatName(sub.nama_subkriteria),
        }));
};

// Notifications
const checkNotif = () => {
    if (pageProps.flash.notif_status) {
        setTimeout(() => {
            toast.add({
                severity: pageProps.flash.notif_status,
                summary: "Notifikasi",
                detail: pageProps.flash.notif_message,
                life: 4000,
                group: "tc",
            });
        }, 1000);
    }
};

// Form Actions
const tambahData = () => {
    showForm.value = false;
    formWarga.post(route("AddWarga"), {
        onSuccess: () => {
            refreshPage();
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Gagal menambahkan data.",
                life: 4000,
            }),
                (showForm.value = true);
        },
    });
};

// Refresh Page
const refreshPage = () => {
    checkNotif();
    showForm.value = false;
    isLoading.value = true;
    router.visit(route("wargapage"));
    setTimeout(() => (isLoading.value = false), 600);
};

// Export DataTable
const exportCSV = () => {
    dt.value.exportCSV();
};
</script>

<template>
    <Head title="Data Warga" />
    <Toast position="top-center" group="tc" />
    <ConfirmDialog />
    <TemplateLayout :auth="auth">
        <template #pageContent>
            <div class="p-4 space-y-4">
                <!-- Header -->
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-semibold">Data Warga</h1>
                    <Button
                        label="Tambah Data"
                        icon="pi pi-plus"
                        severity="success"
                        @click="showForm = true"
                    />
                </div>

                <!-- Dialog: Tambah Data -->
                <Dialog
                    header="Tambah Data Warga"
                    :visible="showForm"
                    modal
                    :style="{ width: '70%' }"
                    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                >
                    <form @submit.prevent="tambahData">
                        <div
                            class="grid gap-4 mt-2 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3"
                        >
                            <!-- Periode -->
                            <div>
                                <FloatLabel variant="on">
                                    <Select
                                        fluid
                                        v-model="formWarga.periode_id"
                                        :options="periode"
                                        optionValue="id"
                                        optionLabel="tahun"
                                        :invalid="!!formWarga.errors.periode_id"
                                    />
                                    <label>Periode</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.periode_id"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.periode_id }}
                                </Message>
                            </div>

                            <!-- Nomor KK -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        v-keyfilter.num
                                        fluid
                                        v-model="formWarga.nomor_kk"
                                        :invalid="!!formWarga.errors.nomor_kk"
                                    />
                                    <label>Nomor KK</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.nomor_kk"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.nomor_kk }}
                                </Message>
                            </div>

                            <!-- Nama KK -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        fluid
                                        v-model="formWarga.nama_kk"
                                        :invalid="!!formWarga.errors.nama_kk"
                                    />
                                    <label>Nama Kepala Keluarga</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.nama_kk"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.nama_kk }}
                                </Message>
                            </div>

                            <!-- Provinsi -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        fluid
                                        v-model="formWarga.provinsi"
                                        :invalid="!!formWarga.errors.provinsi"
                                        disabled
                                    />
                                    <label>Provinsi</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.provinsi"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.provinsi }}
                                </Message>
                            </div>

                            <!-- Kabupaten -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        fluid
                                        v-model="formWarga.kabupaten"
                                        :invalid="!!formWarga.errors.kabupaten"
                                        disabled
                                    />
                                    <label>Kabupaten</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.kabupaten"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.kabupaten }}
                                </Message>
                            </div>

                            <!-- Kampung -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        fluid
                                        v-model="formWarga.kampung"
                                        :invalid="!!formWarga.errors.kampung"
                                        disabled
                                    />
                                    <label>Kampung</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.kampung"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.kampung }}
                                </Message>
                            </div>

                            <!-- RT -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        v-keyfilter.num
                                        fluid
                                        v-model="formWarga.rt"
                                        :invalid="!!formWarga.errors.rt"
                                    />
                                    <label>RT</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.rt"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.rt }}
                                </Message>
                            </div>

                            <!-- RW -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        v-keyfilter.num
                                        fluid
                                        v-model="formWarga.rw"
                                        :invalid="!!formWarga.errors.rw"
                                    />
                                    <label>RW</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.rw"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.rw }}
                                </Message>
                            </div>

                            <!-- Asal Suku -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        fluid
                                        v-model="formWarga.asal_suku"
                                        :invalid="!!formWarga.errors.asal_suku"
                                    />
                                    <label>Asal Suku</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.asal_suku"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.asal_suku }}
                                </Message>
                            </div>

                            <!-- Pekerjaan -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        fluid
                                        v-model="formWarga.pekerjaan"
                                        :invalid="!!formWarga.errors.pekerjaan"
                                    />
                                    <label>Pekerjaan</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.pekerjaan"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.pekerjaan }}
                                </Message>
                            </div>

                            <!-- Agama -->
                            <div>
                                <FloatLabel variant="on">
                                    <Select
                                        fluid
                                        v-model="formWarga.agama"
                                        :options="optionsAgama"
                                        optionValue="value"
                                        optionLabel="label"
                                        :invalid="!!formWarga.errors.agama"
                                    />
                                    <label>Agama</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.agama"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.agama }}
                                </Message>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div>
                                <FloatLabel variant="on">
                                    <Select
                                        fluid
                                        v-model="formWarga.jenis_kelamin"
                                        :options="optionsJk"
                                        optionValue="value"
                                        optionLabel="label"
                                        :invalid="
                                            !!formWarga.errors.jenis_kelamin
                                        "
                                    />
                                    <label>Jenis Kelamin</label>
                                </FloatLabel>
                                <Message
                                    v-if="formWarga.errors.jenis_kelamin"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ formWarga.errors.jenis_kelamin }}
                                </Message>
                            </div>

                            <div v-for="item in kriteria" :key="item.id">
                                <FloatLabel variant="on">
                                    <Select
                                        fluid
                                        v-if="getSubkriteria(item.id).length"
                                        v-model="
                                            formWarga.kriteria_values[
                                                item.nama_kriteria
                                            ]
                                        "
                                        :invalid="
                                            !!formWarga.errors[
                                                `kriteria_values.${item.nama_kriteria}`
                                            ]
                                        "
                                        :options="getSubkriteria(item.id)"
                                        optionLabel="nama_subkriteria_formatted"
                                        optionValue="nama_subkriteria"
                                    />
                                    <label
                                        v-if="getSubkriteria(item.id).length"
                                        >{{
                                            formatName(item.nama_kriteria)
                                        }}</label
                                    >
                                </FloatLabel>

                                <Message
                                    v-if="
                                        formWarga.errors[
                                            `kriteria_values.${item.nama_kriteria}`
                                        ]
                                    "
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{
                                        formWarga.errors[
                                            `kriteria_values.${item.nama_kriteria}`
                                        ]
                                    }}
                                </Message>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-2 mt-4">
                            <Button
                                label="Simpan"
                                type="submit"
                                icon="pi pi-check"
                            />
                            <Button
                                label="Batal"
                                icon="pi pi-times"
                                severity="secondary"
                                @click="showForm = false"
                                type="button"
                            />
                        </div>
                    </form>
                </Dialog>

                <!-- DataTable -->
                <DataTable
                    ref="dt"
                    :value="Wargadata"
                    stripedRows
                    paginator
                    :rows="10"
                    scrollable
                    :filters="filters"
                >
                    <template #empty>
                        <div class="text-center">Tidak ada data</div>
                    </template>
                    <template #header>
                        <div class="flex justify-between items-center">
                            <Button
                                icon="pi pi-external-link"
                                label="Export"
                                @click="exportCSV"
                                size="small"
                            />
                            <IconField>
                                <InputIcon
                                    ><i class="pi pi-search me-4"
                                /></InputIcon>
                                <InputText
                                    v-model="filters.global.value"
                                    placeholder="Cari Data Warga"
                                />
                            </IconField>
                        </div>
                    </template>
                    <Column field="index" header="No" />
                    <Column field="nomor_kk" header="Nomor KK" />
                    <Column field="nama_kk" header="Nama Kepala Keluarga" />
                    <Column field="status_rumah" header="Status Rumah" />
                </DataTable>
            </div>
        </template>
    </TemplateLayout>
</template>

<script setup>
import { onMounted, ref, defineProps, computed, watch } from "vue";
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
    Card,
    Tag,
    FileUpload,
} from "primevue";
import { FilterMatchMode } from "@primevue/core/api";

// Mendefinisikan props yang diterima oleh komponen
const pageProps = defineProps({
    periode: Array,
    kriteria: Array,
    subkriteria: Array,
    warga: Array,
    flash: Object,
    auth: Object,
});

// Mengambil data otentikasi dari props
const auth = pageProps.auth;

// Mendefinisikan state yang diperlukan
const kriteria = ref([]);
const subkriteria = ref([]);
const Wargadata = ref([]);
const showForm = ref(false);
const visibleImport = ref(false);
const src = ref(null);
const errorMessage = ref("");

const formCSV = useForm({
    file: null,
});

const importCSV = () => {
    src.value = null;
    errorMessage.value = "";
    visibleImport.value = true;
    formCSV.file = null;
};

const uploadCSV = () => {
    formCSV.post(route("uploadCSV"), {
        onSuccess: () => {
            refreshPage();
        },
        onError: (errors) => {
            // Handle error display
            console.error(errors);
        },
    });
};

const onFileSelect = (event) => {
    formCSV.file = event.files[0];

    if (!formCSV.file || !formCSV.file.name.endsWith(".csv")) {
        formCSV.errors.file = null;
        errorMessage.value = "Hanya file dengan ekstensi .csv yang diizinkan.";
        return;
    }

    const reader = new FileReader();

    reader.onload = async (e) => {
        src.value = e.target.result;
        errorMessage.value = ""; // Hapus pesan kesalahan jika file valid
    };

    reader.readAsDataURL(formCSV.file);
};

const showFormAdd = () => {
    formWarga.reset();
    showForm.value = true;
    formType.value = "add";
};

const isLoading = ref(false);

// Opsi untuk dropdown Agama
let optionsAgama = [
    { label: "Islam", value: "islam" },
    { label: "Kristen", value: "kristen" },
    { label: "Katolik", value: "katolik" },
    { label: "Hindu", value: "hindu" },
    { label: "Buddha", value: "buddha" },
    { label: "Konghucu", value: "konghucu" },
];

// Opsi untuk dropdown Jenis Kelamin
let optionsJk = [
    { label: "Laki-laki", value: "laki-laki" },
    { label: "Perempuan", value: "perempuan" },
];

// Mengatur form warga
const formWarga = useForm({
    id: "",
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

const showAllData = ref(false);

const tampilkanSemuaData = () => {
    showAllData.value = true; // Set state untuk menampilkan semua data
    formWarga.periode_id = null; // Reset periode_id jika perlu
};

const filteredWargadata = computed(() => {
    if (showAllData.value) {
        return Wargadata.value.map((warga, index) => ({
            ...warga,
            index: index + 1, // Tetap reset index
        })); // Tampilkan semua data jika tombol "Tampilkan Semua Data" ditekan
    }
    if (!formWarga.periode_id) {
        return Wargadata.value
            .filter((warga) => warga.status === "Belum Menerima")
            .map((warga, index) => ({
                ...warga,
                index: index + 1, // Reset index
            })); // Tampilkan data dengan status 0 jika periode belum dipilih
    }
    return Wargadata.value
        .filter(
            (warga) =>
                warga.tahun_id === formWarga.periode_id &&
                warga.status === "Belum Menerima"
        )
        .map((warga, index) => ({
            ...warga,
            index: index + 1, // Reset index
        }));
});

// Data kolom tabel
const tableColumns = computed(() => {
    // Kolom dinamis dari kriteria
    const dynamicColumns = kriteria.value
        .filter((kriteriaItem) =>
            Wargadata.value.length > 0
                ? kriteriaItem.nama_kriteria in Wargadata.value[0]
                : false
        )
        .map((kriteriaItem) => ({
            field: kriteriaItem.nama_kriteria,
            header: formatName(kriteriaItem.nama_kriteria),
        }));

    return [...dynamicColumns];
});

const formatCell = (value) => {
    if (typeof value === "string") {
        return value
            .split("_")
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(" ");
    }
    return value; // Jika bukan string, kembalikan nilai asli
};

watch(
    () => formWarga.periode_id,
    (newValue) => {
        if (newValue) {
            showAllData.value = false; // Reset ketika periode dipilih
        }
    }
);

// Filter untuk DataTable
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Menggunakan utilitas PrimeVue
const toast = useToast();
const confirm = useConfirm();
let dt = ref();

const formType = ref("add");

// Lifecycle hook untuk inisialisasi data
onMounted(() => {
    initializeData();
    checkNotif();
});

// Fungsi untuk menginisialisasi data
const initializeData = () => {
    kriteria.value = Array.isArray(pageProps.kriteria)
        ? pageProps.kriteria
        : [];
    subkriteria.value = Array.isArray(pageProps.subkriteria)
        ? pageProps.subkriteria
        : [];

    // Menyiapkan nilai default untuk form
    kriteria.value.forEach((item) => {
        formWarga.kriteria_values[item.nama_kriteria] = null;
    });

    // Memetakan data warga dengan indeks
    Wargadata.value = Array.isArray(pageProps.warga)
        ? pageProps.warga.map((warga, index) => ({
              index: index + 1,
              ...warga,
              status: warga.status === 1 ? "Telah Menerima" : "Belum Menerima",
          }))
        : []; // Jika pageProps.warga bukan array, set Wargadata ke array kosong
};

// Format nama kolom untuk keterbacaan yang lebih baik
const formatName = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

// Filter subkriteria berdasarkan ID kriteria
const getSubkriteria = (kriteriaId) => {
    const filteredSubkriteria = subkriteria.value
        .filter((sub) => sub.kriteria_id === kriteriaId)
        .map((sub) => ({
            ...sub,
            nama_subkriteria_formatted: formatName(sub.nama_subkriteria),
        }));

    return Array.isArray(filteredSubkriteria) ? filteredSubkriteria : []; // Pastikan mengembalikan array
};

// Fungsi untuk memeriksa notifikasi
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

// Fungsi untuk menambah data warga
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
            });
            showForm.value = true;
        },
    });
};

// Fungsi untuk mengedit data warga
const editData = (data) => {
    formWarga.reset();
    formWarga.id = data.id;
    formWarga.nomor_kk = data.nomor_kk;
    formWarga.periode_id = data.tahun_id;
    formWarga.nama_kk = data.nama_kk;
    formWarga.provinsi = data.provinsi;
    formWarga.rt = data.rt;
    formWarga.rw = data.rw;
    formWarga.asal_suku = data.asal_suku;
    formWarga.pekerjaan = data.pekerjaan;
    formWarga.agama = data.agama;
    formWarga.jenis_kelamin = data.jenis_kelamin;

    const kriteriaArray = Array.isArray(kriteria.value)
        ? kriteria.value
        : Object.values(kriteria.value || {});

    for (const kriteriaItem of kriteriaArray) {
        const namaKriteria = kriteriaItem.nama_kriteria;
        const subkriteriaList = getSubkriteria(kriteriaItem.id);

        // Pastikan subkriteriaList adalah array
        if (!Array.isArray(subkriteriaList)) {
            console.warn(`Subkriteria untuk ${namaKriteria} tidak valid.`);
            formWarga.kriteria_values[namaKriteria] = null; // Atau nilai default lainnya
            continue; // Lanjutkan ke iterasi berikutnya
        }

        // Cek jika nilai dari data ada dalam kriteria_values
        if (
            data &&
            typeof data === "object" &&
            data[namaKriteria] !== undefined
        ) {
            const nilaiKriteria = data[namaKriteria];

            // Mencari subkriteria yang cocok dengan nilai dari kriteria
            const matchedSubkriteria = subkriteriaList.find(
                (sub) => sub.nama_subkriteria === nilaiKriteria
            );

            // Jika ada yang cocok, set nilai ke formWarga.kriteria_values
            if (matchedSubkriteria) {
                formWarga.kriteria_values[namaKriteria] =
                    matchedSubkriteria.nama_subkriteria;
            } else {
                formWarga.kriteria_values[namaKriteria] = null; // Atau nilai default lainnya
            }
        } else {
            // Jika tidak ada nilai, set ke null atau nilai default
            formWarga.kriteria_values[namaKriteria] = null;
        }
    }

    formType.value = "edit";
    showForm.value = true;
};

// Fungsi untuk memperbarui data warga
const updateData = () => {
    formWarga.put(route("UpdateWarga", formWarga.id), {
        onSuccess: () => {
            refreshPage();
            showForm.value = false;
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Gagal memperbarui data.",
                life: 4000,
            });
        },
    });
};

const HitungWaspas = () => {
    formWarga.post(route("seleksi"), {
        onSuccess: () => {
            checkNotif();
        },
    });
};
// const HitungWaspas = () => {
//     formWarga.post(route("seleksi"), {
//         onSuccess: () => {
//             checkNotif();
//         },
//     });
// };

const hapusData = (data) => {
    formWarga.id = data.id;
    confirm.require({
        message: `Yakin ingin menghapus data kriteria : ${data.nama_kk} ?`,
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Batal",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Hapus",
            severity: "danger",
        },
        reject: () => {
            formWarga.reset();
        },
        accept: () => {
            formWarga.delete(route("DeleteWarga", formWarga.id), {
                onSuccess: () => refreshPage(),
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "Error",
                        detail: `Gagal menghapus data ${data.nama_kk}`,
                        life: 4000,
                    });
                },
            });
        },
    });
};

// Fungsi untuk menyegarkan halaman
const refreshPage = () => {
    checkNotif();
    showForm.value = false;
    isLoading.value = true;
    router.visit(route("wargapage"));
    setTimeout(() => (isLoading.value = false), 600);
};

// Fungsi untuk mengekspor DataTable ke CSV
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
                    <div v-if="pageProps.auth.user.role == 'perangkat'">
                        <Button
                            label="Tambah Data"
                            icon="pi pi-plus"
                            size="small"
                            @click="showFormAdd"
                        />
                    </div>
                </div>

                <!-- Dialog: Tambah Data -->
                <Dialog
                    :header="formType === 'add' ? 'Tambah data' : 'Edit data'"
                    :visible="showForm"
                    modal
                    :style="{ width: '70%' }"
                    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                >
                    <form
                        @submit.prevent="
                            formType === 'add' ? tambahData() : updateData()
                        "
                    >
                        <div
                            class="grid gap-4 mt-2 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3"
                        >
                            <!-- Periode -->
                            <div>
                                <FloatLabel variant="on">
                                    <Select
                                        fluid
                                        v-model="formWarga.periode_id"
                                        :options="pageProps.periode"
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

                            <!-- Kriteria -->
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
                                    >
                                        {{ formatName(item.nama_kriteria) }}
                                    </label>
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

                        <!-- Tombol Simpan dan Batal -->
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

                <Dialog
                    v-model:visible="visibleImport"
                    modal
                    header="Import file CSV"
                    :style="{ width: '25vw' }"
                    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                >
                    <form @submit.prevent="uploadCSV">
                        <div class="card flex flex-col items-center gap-6">
                            <FileUpload
                                mode="basic"
                                @select="onFileSelect"
                                customUpload
                                auto
                                size="small"
                                severity="secondary"
                                class="p-button-outlined"
                            />
                            <p v-if="errorMessage" class="text-red-500 text-sm">
                                {{ errorMessage }}
                            </p>
                            <Message
                                v-if="formCSV.errors.file"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ formCSV.errors.file }}
                            </Message>
                            <div
                                v-if="src"
                                class="w-full sm:w-64 flex flex-col items-center"
                            >
                                <p class="text-sm text-gray-500">
                                    File CSV telah dipilih:
                                </p>
                                <img
                                    src="csv.png"
                                    alt="CSV Icon"
                                    class="w-16 h-16"
                                />
                                <p class="text-sm text-gray-500 mt-2">
                                    File CSV berhasil dimuat.
                                </p>
                            </div>
                            <Button
                                type="submit"
                                label="Upload"
                                size="small"
                                :disabled="formCSV.file === null ? true : false"
                                icon="pi pi-upload"
                                variant="outlined"
                            />
                        </div>
                    </form>
                </Dialog>

                <form
                    class="flex items-center justify-between"
                    @submit.prevent="HitungWaspas"
                >
                    <div>
                        <Button
                            label="Tampilkan Semua Data"
                            icon="pi pi-eye"
                            size="small"
                            @click="tampilkanSemuaData"
                            class="me-5"
                        />

                        <Button
                            v-if="pageProps.auth.user.role === 'perangkat'"
                            label="import CSV"
                            size="small"
                            severity="success"
                            icon="pi pi-file-excel"
                            @click="importCSV"
                        />
                    </div>

                    <div class="flex w-[30%] items-center">
                        <div class="w-full">
                            <FloatLabel variant="on">
                                <Select
                                    fluid
                                    id="periode_tabel"
                                    v-model="formWarga.periode_id"
                                    :options="pageProps.periode"
                                    optionValue="id"
                                    optionLabel="tahun"
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

                        <Button
                            v-if="pageProps.auth.user.role == 'kepala'"
                            class="ms-2"
                            icon="pi pi-calculator"
                            label="Seleksi"
                            size="small"
                            type="submit"
                        />
                    </div>
                </form>

                <!-- DataTable untuk menampilkan data warga -->
                <Card>
                    <!-- Header tabel -->
                    <template #header>
                        <div class="flex justify-between items-center p-4">
                            <Button
                                icon="pi pi-external-link"
                                label="Export"
                                @click="exportCSV"
                                size="small"
                            />
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search me-4" />
                                </InputIcon>
                                <InputText
                                    v-model="filters.global.value"
                                    placeholder="Cari Data Warga"
                                />
                            </IconField>
                        </div>
                    </template>
                    <template #content>
                        <DataTable
                            ref="dt"
                            :value="filteredWargadata"
                            stripedRows
                            paginator
                            :rows="10"
                            scrollable
                            responsiveLayout="scroll"
                            :filters="filters"
                            resizableColumns
                            :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                            columnResizeMode="fit"
                            size="large"
                        >
                            <!-- Pesan jika data kosong -->
                            <template #empty>
                                <div class="text-left">
                                    Data Warga tidak Ditemukan
                                </div>
                            </template>

                            <Column
                                frozen
                                alignFrozen="left"
                                field="index"
                                header="No"
                            />

                            <Column
                                sortable
                                field="tahun"
                                frozen
                                header="Periode"
                            >
                                <template #body="{ data }">
                                    {{ formatName(data.tahun) }}
                                </template>
                            </Column>
                            <Column
                                sortable
                                field="nama_kk"
                                frozen
                                header="Nama Warga"
                            >
                                <template #body="{ data }">
                                    {{ formatName(data.nama_kk) }}
                                </template>
                            </Column>
                            <Column field="nomor_kk" header="Nomor KK" />
                            <Column sortable field="provinsi" header="Provinsi">
                                <template #body="{ data }">
                                    {{ formatName(data.provinsi) }}
                                </template>
                            </Column>
                            <Column
                                sortable
                                field="kabupaten"
                                header="Kabupaten"
                            >
                                <template #body="{ data }">
                                    {{ formatName(data.kabupaten) }}
                                </template>
                            </Column>
                            <Column sortable field="kampung" header="Kampung">
                                <template #body="{ data }">
                                    {{ formatName(data.kampung) }}
                                </template>
                            </Column>
                            <Column
                                sortable
                                field="asal_suku"
                                header="Asal Suku"
                            >
                                <template #body="{ data }">
                                    {{ formatName(data.asal_suku) }}
                                </template>
                            </Column>
                            <Column sortable field="agama" header="Agama">
                                <template #body="{ data }">
                                    {{ formatName(data.agama) }}
                                </template>
                            </Column>
                            <Column
                                sortable
                                field="jenis_kelamin"
                                header="Jenis Kelamin"
                            >
                                <template #body="{ data }">
                                    {{ formatName(data.jenis_kelamin) }}
                                </template>
                            </Column>
                            <Column sortable field="rt" header="RT">
                                <template #body="{ data }">
                                    {{ formatName(data.rt) }}
                                </template>
                            </Column>
                            <Column sortable field="rw" header="RW">
                                <template #body="{ data }">
                                    {{ formatName(data.rw) }}
                                </template>
                            </Column>
                            <Column
                                sortable
                                field="pekerjaan"
                                header="Pekerjaan"
                            >
                                <template #body="{ data }">
                                    {{ formatName(data.pekerjaan) }}
                                </template>
                            </Column>

                            <!-- Render kolom -->
                            <!-- Header dan Field -->
                            <template
                                v-for="col in tableColumns"
                                :key="col.field"
                            >
                                <Column
                                    :header="col.header"
                                    sortable
                                    :field="[col.field]"
                                >
                                    <template #body="{ data }">
                                        {{ formatCell(data[col.field]) }}
                                    </template>
                                </Column>
                            </template>

                            <Column
                                sortable
                                frozen
                                alignFrozen="right"
                                header="Status"
                                field="status"
                            >
                                <template #body="{ data }">
                                    <div v-if="data.status == 'Telah Menerima'">
                                        <Tag
                                            icon="pi pi-check"
                                            severity="success"
                                            value="Telah menerima"
                                        ></Tag>
                                    </div>
                                    <div v-else>
                                        <Tag
                                            icon="pi pi-times"
                                            severity="danger"
                                            value="Belum menerima"
                                        ></Tag>
                                    </div>
                                </template>
                            </Column>

                            <div v-if="pageProps.auth.user.role == 'perangkat'">
                                <Column
                                    header="Opsi"
                                    frozen
                                    alignFrozen="right"
                                >
                                    <template #body="{ data }">
                                        <div class="flex gap-2 items-center">
                                            <Button
                                                size="small"
                                                @click="editData(data)"
                                                icon="pi pi-pen-to-square"
                                                severity="info"
                                                outlined
                                            />
                                            <Button
                                                size="small"
                                                @click="hapusData(data)"
                                                icon="pi pi-trash"
                                                severity="danger"
                                                outlined
                                            />
                                        </div>
                                    </template>
                                </Column>
                            </div>
                        </DataTable>
                    </template>
                </Card>
            </div>
        </template>
    </TemplateLayout>
</template>

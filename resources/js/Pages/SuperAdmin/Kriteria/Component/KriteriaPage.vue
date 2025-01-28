<script setup>
import { onMounted, ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { FilterMatchMode } from "@primevue/core/api";

// import component
import {
    Card,
    DataTable,
    Column,
    Button,
    ConfirmDialog,
    useConfirm,
    Dialog,
    InputText,
    InputNumber,
    InputIcon,
    IconField,
    FloatLabel,
    Message,
    Select,
    TabView,
    TabPanel,
} from "primevue";

const props = defineProps({
    dataKriteria: Object,
    Aktif: Object,
    Taktif: Object,
});

const activeTabIndex = ref(0); // Set default to 0 for the "Aktif" tab

let dataFix = ref({
    Aktif: [],
    Tidak: [],
});

onMounted(() => {
    dataFix.value.Aktif = props.Aktif
        ? props.Aktif.map((item, index) => ({
              index: index + 1,
              persen: item.bobot + "%",
              nama_kriteria_formatted: formatName(item.nama_kriteria),
              ...item,
          }))
        : [];

    dataFix.value.Tidak = props.Taktif
        ? props.Taktif.map((item, index) => ({
              index: index + 1,
              persen: item.bobot + "%",
              nama_kriteria_formatted: formatName(item.nama_kriteria),
              ...item,
          }))
        : [];
});

const filteredData = computed(() => {
    // Check if dataFix and the activeTab exist
    const activeTabValue = activeTabIndex.value === 0 ? "Aktif" : "Tidak";
    if (dataFix.value[activeTabValue]) {
        return dataFix.value[activeTabValue].filter(
            (item) => item.status === activeTabValue
        );
    }
    return []; // Return an empty array if the data is not available
});

const emit = defineEmits(["refreshPage"]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const exportCSV = () => {
    dt.value.exportCSV();
};

let dt = ref();

let showUbahForm = ref(false);

const confirm = useConfirm();

let hapusForm = useForm({
    id: null,
    kode_kriteria: null,
    tipe: null,
});

let kriteriaForm = useForm({
    id: null,
    nama: null,
    tipe: null,
    nilai_bobot: null,
    status: null,
});

// Fungsi untuk memformat nama kolom
const formatName = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

let tipe = [{ nama: "Cost" }, { nama: "Benefit" }];
let status = [{ nama: "Aktif" }, { nama: "Tidak" }];
let keterangan = [{ nama: "Penting" }, { nama: "Tidak" }];

const lihatData = (index) => {
    kriteriaForm.clearErrors();
    showUbahForm.value = true;
    const activeTabValue = activeTabIndex.value === 0 ? "Aktif" : "Tidak";
    const data = dataFix.value[activeTabValue][index - 1];
    kriteriaForm.id = data.id;
    kriteriaForm.kode_kriteria = data.kode_kriteria;
    kriteriaForm.nama = formatName(data.nama_kriteria);
    kriteriaForm.nilai_bobot = data.bobot;
    kriteriaForm.tipe = data.tipe;
    kriteriaForm.status = data.status;
};

const isDisabled = ref(false);

watch(
    () => kriteriaForm.status,
    (newValue) => {
        if (newValue === "Tidak") {
            kriteriaForm.nilai_bobot = null; // Set bobot to null
            kriteriaForm.tipe = null; // Set tipe to null
            isDisabled.value = true; // Disable fields
        } else {
            isDisabled.value = false;
        }
    }
);

const hapusData = (id, kode_kriteria) => {
    hapusForm.id = id;

    confirm.require({
        message: `Yakin ingin menghapus data kriteria : ${kode_kriteria} ?`,
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
            hapusForm.reset();
        },
        accept: () => {
            hapusForm.delete(route("DeleteKriteria", hapusForm.id), {
                onSuccess: () => emit("refreshPage"),
            });
        },
    });
};

const updateData = (id) => {
    confirm.require({
        message: `Simpan Data ?`,
        header: "Peringatan",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Batal",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Ya",
            severity: "info",
        },
        reject: () => {
            kriteriaForm.reset();
        },
        accept: () => {
            showUbahForm.value = false;
            kriteriaForm.put(route("UpdateKriteria", kriteriaForm.id), {
                onSuccess: () => emit("refreshPage"),
                onError: () => (showUbahForm.value = true),
            });
        },
    });
};
</script>

<template>
    <!-- Dialog ubah data kriteria -->
    <Dialog
        modal
        header="Ubah Data Kriteria"
        :style="{ width: '25rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        v-model:visible="showUbahForm"
    >
        <form
            @submit.prevent="updateData(kriteriaForm.id)"
            class="flex flex-col gap-4 mt-1"
            autocomplete="off"
        >
            <!-- Data form -->
            <div>
                <FloatLabel variant="on" class="w-full">
                    <InputText
                        fluid
                        v-model="kriteriaForm.nama"
                        id="nama"
                        autocomplete="off"
                        :invalid="!!kriteriaForm.errors.nama"
                    />
                    <label for="nama">Nama Kriteria</label>
                </FloatLabel>
                <Message
                    v-if="kriteriaForm.errors.nama"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ kriteriaForm.errors.nama }}
                </Message>
            </div>

            <div>
                <FloatLabel variant="on">
                    <Select
                        id="status"
                        v-model="kriteriaForm.status"
                        :options="status"
                        optionLabel="nama"
                        optionValue="nama"
                        fluid
                        :invalid="!!kriteriaForm.errors.status"
                    />
                    <label for="status">Status</label>
                </FloatLabel>
                <Message
                    v-if="kriteriaForm.errors.status"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ kriteriaForm.errors.status }}
                </Message>
            </div>

            <div>
                <FloatLabel variant="on" class="w-full">
                    <InputNumber
                        fluid
                        v-model="kriteriaForm.nilai_bobot"
                        inputId="nilai"
                        mode="decimal"
                        showButtons
                        :min="0"
                        :max="100"
                        :invalid="!!kriteriaForm.errors.nilai_bobot"
                        :disabled="isDisabled"
                    />
                    <label for="nilai">Nilai Bobot (%)</label>
                </FloatLabel>
                <Message
                    v-if="kriteriaForm.errors.nilai_bobot"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ kriteriaForm.errors.nilai_bobot }}
                </Message>
            </div>

            <div>
                <FloatLabel variant="on">
                    <Select
                        id="tipe"
                        v-model="kriteriaForm.tipe"
                        :options="tipe"
                        optionLabel="nama"
                        optionValue="nama"
                        fluid
                        :invalid="!!kriteriaForm.errors.tipe"
                        :disabled="isDisabled"
                    />
                    <label for="type">Type</label>
                </FloatLabel>
                <Message
                    v-if="kriteriaForm.errors.tipe"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ kriteriaForm.errors.tipe }}
                </Message>
            </div>

            <!-- Tombol aksi -->
            <div class="flex justify-end gap-2">
                <Button
                    type="button"
                    label="Batal"
                    severity="danger"
                    @click="(showUbahForm = false), kriteriaForm.reset()"
                />
                <Button type="submit" label="Simpan Data" severity="info" />
            </div>
        </form>
    </Dialog>

    <!-- Dialog ubah data kriteria selesai -->
    <ConfirmDialog style="width: 24rem" />
    <Card>
        <template #content>
            <!-- TabView untuk mengelompokkan data berdasarkan status -->
            <TabView v-model:activeIndex="activeTabIndex">
                <TabPanel header="Aktif" value="Aktif">
                    <DataTable
                        removableSort
                        v-model:filters="filters"
                        :value="filteredData"
                        paginator
                        :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                        :rows="10"
                    >
                        <template #header>
                            <div class="flex items-center justify-end">
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText
                                        v-model="filters['global'].value"
                                        placeholder="Cari Data Kriteria"
                                    />
                                </IconField>
                            </div>
                        </template>
                        <template #empty>
                            <span
                                class="flex justify-center items-center text-lg"
                                >Tidak ada data</span
                            >
                        </template>
                        <Column sortable field="index" header="No" />
                        <Column
                            sortable
                            field="nama_kriteria_formatted"
                            header="Nama Kriteria"
                        />
                        <Column sortable field="persen" header="Nilai Bobot" />
                        <Column sortable field="tipe" header="jenis" />
                        <Column header="Opsi">
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <Button
                                        size="small"
                                        @click="lihatData(data.index)"
                                        icon="pi pi-pen-to-square"
                                        iconPos="right"
                                        severity="info"
                                        outlined
                                    />
                                    <Button
                                        size="small"
                                        @click="
                                            hapusData(
                                                data.id,
                                                data.kode_kriteria
                                            )
                                        "
                                        icon="pi pi-trash"
                                        iconPos="right"
                                        severity="danger"
                                        outlined
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>
                <TabPanel header="Tidak Aktif" value="Tidak">
                    <DataTable
                        removableSort
                        v-model:filters="filters"
                        :value="filteredData"
                        paginator
                        :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                        :rows="10"
                    >
                        <!-- Template serupa seperti tab Aktif -->
                        <template #header>
                            <div class="flex items-center justify-end">
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText
                                        v-model="filters['global'].value"
                                        placeholder="Cari Data Kriteria"
                                    />
                                </IconField>
                            </div>
                        </template>
                        <template #empty>
                            <span
                                class="flex justify-center items-center text-lg"
                                >Tidak ada data</span
                            >
                        </template>
                        <Column sortable field="index" header="No" />
                        <Column
                            sortable
                            field="nama_kriteria_formatted"
                            header="Nama Kriteria"
                        />
                        <Column header="Opsi">
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <Button
                                        size="small"
                                        @click="lihatData(data.index)"
                                        icon="pi pi-pen-to-square"
                                        iconPos="right"
                                        severity="info"
                                        outlined
                                    />
                                    <Button
                                        size="small"
                                        @click="
                                            hapusData(
                                                data.id,
                                                data.kode_kriteria
                                            )
                                        "
                                        icon="pi pi-trash"
                                        iconPos="right"
                                        severity="danger"
                                        outlined
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>
            </TabView>
        </template>
    </Card>
</template>

<style scoped></style>

<script setup>
import { onMounted, ref } from "vue";
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
} from "primevue";

const props = defineProps({
    dataKriteria: Object,
});

onMounted(() => {
    dataFix.value = props.dataKriteria.map((item, index) => ({
        index: index + 1,
        persen: item.bobot + "%",
        nama_kriteria_formatted: formatName(item.nama_kriteria), // Properti baru dengan nama diformat
        ...item,
    }));
});

const emit = defineEmits(["refreshPage"]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const exportCSV = () => {
    dt.value.exportCSV();
};

let dt = ref();

let dataFix = ref([]);

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
});

// Fungsi untuk memformat nama kolom
const formatName = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

let tipe = [{ nama: "Cost" }, { nama: "Benefit" }];

const lihatData = (index) => {
    kriteriaForm.clearErrors();
    showUbahForm.value = true;
    kriteriaForm.id = dataFix.value[index - 1]["id"];
    kriteriaForm.kode_kriteria = dataFix.value[index - 1]["kode_kriteria"];
    kriteriaForm.nama = formatName(dataFix.value[index - 1]["nama_kriteria"]);
    kriteriaForm.nilai_bobot = dataFix.value[index - 1]["bobot"];
    kriteriaForm.tipe = dataFix.value[index - 1]["tipe"];
};

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
            <DataTable
                removableSort
                v-model:filters="filters"
                ref="dt"
                :value="dataFix"
                paginator
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
                                placeholder="Cari Data Kriteria"
                            />
                        </IconField>
                    </div>
                </template>
                <template #empty>
                    <span class="flex justify-center items-center text-lg"
                        >Tidak ada data</span
                    >
                </template>
                <Column sortable field="index" header="No" />
                <Column sortable field="kode_kriteria" header="Id Kriteria" />
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
                                @click="hapusData(data.id, data.kode_kriteria)"
                                icon="pi pi-trash"
                                iconPos="right"
                                severity="danger"
                                outlined
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>
</template>

<style scoped></style>

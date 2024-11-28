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
    Select,
    InputIcon,
    IconField,
    FloatLabel,
    Message,
} from "primevue";

const props = defineProps({ dataKriteria: Object, dataSubKriteria: Object });

onMounted(() => {
    dataSubKriteriaFix.value = props.dataSubKriteria.map((p, i) => ({
        index: i + 1,
        nama_sub_kriteria: formatNameSub(p.nama_subkriteria),
        ...p,
    }));
});

const emit = defineEmits(["refreshPage", "errorToast"]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const exportCSV = () => {
    dt.value.exportCSV();
};

let dt = ref();

let dataSubKriteriaFix = ref([]);

const confirm = useConfirm();

const showUbahData = ref(false);

let hapusForm = useForm({
    id: null,
});

let ubahForm = useForm({
    id: null,
    nama_sub: null,
    nilai_bobot: null,
    id_relasi: null,
});

// Fungsi untuk memformat nama kolom
const formatNameSub = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

const lihatData = (index) => {
    ubahForm.clearErrors();
    showUbahData.value = true;
    ubahForm.id = dataSubKriteriaFix.value[index - 1]["id"];
    ubahForm.nama_sub = formatNameSub(
        dataSubKriteriaFix.value[index - 1]["nama_subkriteria"]
    );
    ubahForm.nilai_bobot = dataSubKriteriaFix.value[index - 1]["nilai"];
    ubahForm.id_relasi = dataSubKriteriaFix.value[index - 1]["kriteria_id"];
};

const updateData = (id) => {
    confirm.require({
        message: `Simpan perubahan ?`,
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
            ubahForm.reset();
        },
        accept: () => {
            showUbahData.value = false;
            ubahForm.put(route("UpdateSub", ubahForm.id), {
                onSuccess: () => emit("refreshPage"),
                onError: () => {
                    showUbahData.value = true;
                    emit("errorToast");
                },
            });
        },
    });
};

const hapusData = (id, kode) => {
    hapusForm.id = id;

    confirm.require({
        message: `Yakin ingin menghapus data kriteria : ${kode} ?`,
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
            hapusForm.delete(route("DeleteSub", hapusForm.id), {
                onSuccess: () => emit("refreshPage"),
            });
        },
    });
};
</script>

<template>
    <!-- Dialog ubah data subkriteria -->
    <Dialog
        modal
        header="Ubah Data Sub Kriteria"
        :style="{ width: '25rem' }"
        :breakpoints="{ '1199px': '30rem', '575px': '90vw' }"
        v-model:visible="showUbahData"
    >
        <form
            @submit.prevent="updateData(ubahForm.id)"
            class="flex flex-col gap-4 mt-1"
            autocomplete="off"
        >
            <!-- Nama Kriteria -->
            <div class="w-full">
                <FloatLabel variant="on">
                    <InputText
                        fluid
                        v-model="ubahForm.nama_sub"
                        id="nama"
                        :invalid="!!ubahForm.errors.nama_sub"
                        autocomplete="off"
                    />
                    <label for="nama">Nama Kriteria</label>
                </FloatLabel>
                <Message
                    v-if="ubahForm.errors.nama_sub"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ ubahForm.errors.nama_sub }}
                </Message>
            </div>

            <!-- Nilai Bobot -->
            <div class="w-full">
                <FloatLabel variant="on">
                    <InputNumber
                        fluid
                        v-model="ubahForm.nilai_bobot"
                        inputId="nilai"
                        mode="decimal"
                        showButtons
                        :min="0"
                        :max="100"
                        :invalid="!!ubahForm.errors.nilai_bobot"
                    />
                    <label for="nilai">Nilai Bobot</label>
                </FloatLabel>
                <Message
                    v-if="ubahForm.errors.nilai_bobot"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ ubahForm.errors.nilai_bobot }}
                </Message>
            </div>

            <!-- ID Kriteria -->
            <div class="w-full">
                <FloatLabel variant="on">
                    <Select
                        fluid
                        v-model="ubahForm.id_relasi"
                        :options="props.dataKriteria"
                        optionLabel="kode_kriteria"
                        optionValue="id"
                        :invalid="!!ubahForm.errors.id_relasi"
                    />
                    <label for="id_relasi">ID Kriteria</label>
                </FloatLabel>
                <Message
                    v-if="ubahForm.errors.id_relasi"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ ubahForm.errors.id_relasi }}
                </Message>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-2">
                <Button
                    type="button"
                    label="Batal"
                    outlined
                    severity="danger"
                    @click="showUbahData = false"
                />
                <Button
                    type="submit"
                    label="Simpan Data"
                    outlined
                    severity="info"
                />
            </div>
        </form>
    </Dialog>
    <!-- Dialog ubah data subkriteria selesai-->

    <!-- Dialog ubah data subkriteria selesai-->

    <ConfirmDialog style="width: 24rem" />
    <Card>
        <template #content>
            <DataTable
                removableSort
                v-model:filters="filters"
                ref="dt"
                :value="dataSubKriteriaFix"
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
                                placeholder="Cari Data Sub Kriteria"
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
                <Column sortable field="kode_sub" header="Kode Sub" />
                <Column
                    sortable
                    field="nama_sub_kriteria"
                    header="Nama Kriteria"
                />
                <Column sortable field="nilai" header="Nilai Bobot" />
                <Column
                    sortable
                    field="kriteria.kode_kriteria"
                    header="Kode Kriteria"
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
                                @click="hapusData(data.id, data.kode_sub)"
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

<script setup>
import { onMounted, ref } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
// import layout
import Layout from "@/Layouts/TemplateLayout.vue";

// import component
import {
    Button,
    Dialog,
    InputText,
    Card,
    DataTable,
    Toast,
    useToast,
    FloatLabel,
    Message,
    IconField,
    Column,
    InputIcon,
    Select,
    ConfirmDialog,
    useConfirm,
} from "primevue";

import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    periode: Object,
    auth: Object,
    flash: Object,
});

let dataPeriode = ref([]);

const exportCSV = () => {
    dt.value.exportCSV();
};

let dt = ref();

onMounted(() => {
    dataPeriode.value = props.periode.map((p, i) => ({
        index: i + 1,
        ...p,
    }));

    checkNotif();
});

console.log(dataPeriode.value);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const toast = useToast();

const isLoading = ref(false);

const currentYear = new Date().getFullYear();
const yearOptions = ref(
    Array.from({ length: 20 }, (_, i) => ({
        label: `${currentYear - i}`,
        value: `${currentYear - i}`,
    }))
);

let showPeriodeForm = ref(false);
let editPeriodeForm = ref(false);

const checkNotif = () => {
    if (props.flash && props.flash.notif_status) {
        setTimeout(() => {
            if (props.flash.notif_status === "success") {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: props.flash.notif_message,
                    life: 4000,
                    group: "tc",
                });
            } else {
                toast.add({
                    severity: "error",
                    summary: "Info",
                    detail: props.flash.notif_message,
                    life: 4000,
                    group: "tc",
                });
            }
        }, 1000);
    }
};

const refreshPage = () => {
    checkNotif();
    showPeriodeForm.value = false;
    isLoading.value = true;
    router.visit(route("periode"));
    setTimeout(() => (isLoading.value = false), 600);
};

const periodeForm = useForm({
    tahun: "",
});

const periodeEdit = useForm({
    id: "",
    tahun: null,
});

const tambahPeriode = () => {
    showPeriodeForm.value = false;

    periodeForm.post(route("AddPeriode"), {
        onSuccess: () => refreshPage(),
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Notification",
                detail: "Gagal menambahkan data periode :(",
                life: 4000,
                group: "tc",
            });
            showPeriodeForm.value = true;
        },
    });
};

const lihatData = (data) => {
    if (data) {
        periodeEdit.clearErrors();
        editPeriodeForm.value = true;
        periodeEdit.id = data.id;
        periodeEdit.tahun = data.tahun;
    } else {
        console.error("Invalid data for editing", data);
    }
};

const editPeriode = () => {
    editPeriodeForm.value = false;
    periodeEdit.put(route("EditPeriode", periodeEdit.id), {
        onSuccess: () => refreshPage(),
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Notification",
                detail: "Gagal update data periode :(",
                life: 4000,
                group: "tc",
            });
            editPeriodeForm.value = true;
        },
    });
};

const hapusPeriode = useForm({
    id_menu: "",
    nama_menu: "",
});

const confirm = useConfirm();
const confirmDelete = (id, tahun) => {
    hapusPeriode.id = id;
    hapusPeriode.tahun = tahun;

    confirm.require({
        message: `Yakin ingin menghapus data record ${tahun}?`,
        header: "Peringatan",
        icon: "pi pi-info-circle",
        rejectLabel: "Cancel",
        rejectProps: {
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Delete",
            severity: "danger",
        },
        accept: () => {
            hapusPeriode.delete(route("DeletePeriode", hapusPeriode.id), {
                onSuccess: () => refreshPage(),
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "Notification",
                        detail: "Gagal menghapus menghapus periode :(",
                        life: 4000,
                        group: "tc",
                    });
                    editPeriodeForm.value = true;
                },
            });
        },
        reject: () => {
            toast.add({
                severity: "error",
                summary: "Batal",
                detail: "Batal menghapus data ",
                life: 3000,
                group: "tc",
            });
        },
    });
};
</script>

<template>
    <Head title="Periode" />
    <ConfirmDialog></ConfirmDialog>
    <Layout :auth="auth">
        <template #pageContent>
            <Toast position="top-center" group="tc" />
            <div class="flex gap-4 p-4 justify-between">
                <h1 class="text-xl font-semibold">Data Periode</h1>

                <Button
                    @click="showPeriodeForm = true"
                    label="Tambah Data"
                    size="small"
                    class="w-48"
                    icon="pi pi-plus-circle"
                />
            </div>

            <Dialog
                modal
                header="Tambah Periode"
                :style="{ width: '25rem' }"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                v-model:visible="showPeriodeForm"
            >
                <form @submit.prevent="tambahPeriode" autocomplete="off">
                    <!-- Data Form -->
                    <div class="flex items-center flex-col gap-4 mb-4 mt-1">
                        <div class="w-full">
                            <FloatLabel variant="on">
                                <Select
                                    fluid
                                    class="text-slate-950"
                                    id="select_tahun"
                                    v-model="periodeForm.tahun"
                                    :options="yearOptions"
                                    optionValue="label"
                                    optionLabel="value"
                                    :invalid="!!periodeForm.errors.tahun"
                                />
                                <label for="select_tahun">Tahun</label>
                            </FloatLabel>
                            <Message
                                v-if="periodeForm.errors.tahun"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ periodeForm.errors.tahun }}
                            </Message>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <Button
                            type="submit"
                            label="Simpan Data"
                            severity="info"
                            size="small"
                        />
                    </div>
                </form>
            </Dialog>

            <Dialog
                modal
                header="Tambah Periode"
                :style="{ width: '25rem' }"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                v-model:visible="editPeriodeForm"
            >
                <form @submit.prevent="editPeriode" autocomplete="off">
                    <!-- Data Form -->
                    <div class="flex items-center flex-col gap-4 mb-4 mt-1">
                        <div class="w-full">
                            <FloatLabel variant="on">
                                <Select
                                    fluid
                                    class="text-slate-950"
                                    id="edit_tahun"
                                    v-model="periodeEdit.tahun"
                                    :options="yearOptions"
                                    optionValue="label"
                                    optionLabel="value"
                                    :invalid="!!periodeEdit.errors.tahun"
                                />
                                <label for="edit_tahun">Tahun</label>
                            </FloatLabel>
                            <Message
                                v-if="periodeEdit.errors.tahun"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ periodeEdit.errors.tahun }}
                            </Message>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <Button
                            type="submit"
                            label="Simpan Data"
                            severity="info"
                            size="small"
                        />
                    </div>
                </form>
            </Dialog>

            <Card>
                <template #content>
                    <DataTable
                        removableSort
                        v-model:filters="filters"
                        ref="dt"
                        :value="dataPeriode"
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
                        <Column sortable field="tahun" header="Periode" />
                        <Column header="Opsi">
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <Button
                                        size="small"
                                        @click="lihatData(data)"
                                        icon="pi pi-pen-to-square"
                                        iconPos="right"
                                        severity="info"
                                        outlined
                                    />
                                    <Button
                                        size="small"
                                        @click="
                                            confirmDelete(data.id, data.tahun)
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
                </template>
            </Card>
        </template>
    </Layout>
</template>

<style scoped></style>

<script setup>
import { onMounted, ref } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import TemplateLayout from "@/Layouts/TemplateLayout.vue";
import {
    Toast,
    useToast,
    Dialog,
    Button,
    InputText,
    Password,
    Select,
    Message,
    FloatLabel,
    Tag,
    DataTable,
    Column,
    Card,
    ConfirmDialog,
    useConfirm,
} from "primevue";
import { FilterMatchMode } from "@primevue/core/api";

// Props
let props = defineProps({
    users: Object,
    flash: Object,
    auth: Object,
    errors: Object,
});

// State
let dt = ref();
let dataUser = ref([]);
let showForm = ref(false);
const toast = useToast();
const formMode = ref("add");
const isLoading = ref(false);

const showAdd = () => {
    showForm.value = true;
    formMode.value = "add";
    userForm.reset();
    userForm.clearErrors();
};

const exportCSV = () => {
    dt.value.exportCSV();
};

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// User Form
const userForm = useForm({
    id: "",
    nama: "",
    username: "",
    password: "",
    password_baru: "",
    role: "",
});

// Role Options
let roleOptions = [
    { name: "Warga", value: "warga" },
    { name: "Kepala Kampung", value: "kepala" },
    { name: "Perangkat Kampung", value: "perangkat" },
];

// Mount
onMounted(() => {
    dataUser.value = props.users.map((p, i) => ({
        index: i + 1,
        ...p,
    }));

    checkNotif();
});

// Format nama kolom untuk keterbacaan yang lebih baik
const formatName = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

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
    EditForm.value = false;
    isLoading.value = true;
    router.visit(route("super_admin.pengguna"));
    setTimeout(() => (isLoading.value = false), 600);
};

// Save Data
const SimpanData = () => {
    showForm.value = false;
    userForm.post(route("tambahUser"), {
        onSuccess: () => {
            refreshPage();
        },
        onError: () => {
            showForm.value = true;
        },
    });
};

const EditForm = (data) => {
    if (data) {
        userForm.reset();
        userForm.clearErrors();
        showForm.value = true;
        userForm.id = data.id;
        userForm.nama = data.nama;
        userForm.username = data.username;
        userForm.role = data.role;
        formMode.value = "edit";
    } else {
        toast.add({
            severity: "error",
            summary: "Info",
            detail: "Data tidak ditemukan..",
            life: 4000,
            group: "tc",
        });
    }
};

const UpdateData = () => {
    userForm.put(route("updateUser", userForm.id), {
        onSuccess: () => {
            refreshPage();
        },
        onError: () => {
            showForm.value = true;
        },
    });
};

const confirm = useConfirm();
const confirmDelete = (data) => {
    confirm.require({
        message: `Yakin ingin menghapus data record ${data.nama}?`,
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
            userForm.delete(route("deleteUser", data.id), {
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
    <Head title="Pengguna" />
    <TemplateLayout :auth="auth">
        <template #pageContent>
            <div class="p-4">
                <Toast position="top-center" group="tc" />
                <ConfirmDialog></ConfirmDialog>
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-xl font-semibold">Daftar Pengguna</h1>
                    <Button
                        label="Tambah Data"
                        icon="pi pi-plus"
                        @click="showAdd"
                    />
                </div>

                <!-- Modal -->
                <Dialog
                    :header="
                        formMode == 'add' ? 'Tambah Pengguna' : 'Edit Pengguna'
                    "
                    v-model:visible="showForm"
                    :style="{ width: '30rem' }"
                    modal
                    :breakpoints="{ '960px': '75vw', '640px': '95vw' }"
                >
                    <form
                        @submit.prevent="
                            formMode == 'add' ? SimpanData() : UpdateData()
                        "
                        autocomplete="off"
                    >
                        <div class="grid grid-cols-1 gap-4 mt-2">
                            <!-- Nama -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        fluid
                                        id="nama"
                                        v-model="userForm.nama"
                                        :invalid="!!userForm.errors.nama"
                                    />
                                    <label for="nama">Nama Lengkap</label>
                                </FloatLabel>
                                <Message
                                    v-if="userForm.errors.nama"
                                    severity="error"
                                    variant="simple"
                                    size="small"
                                >
                                    {{ userForm.errors.nama }}
                                </Message>
                            </div>

                            <!-- Username -->
                            <div>
                                <FloatLabel variant="on">
                                    <InputText
                                        fluid
                                        id="username"
                                        v-model="userForm.username"
                                        :invalid="!!userForm.errors.username"
                                    />
                                    <label for="username">Username</label>
                                </FloatLabel>
                                <Message
                                    v-if="userForm.errors.username"
                                    severity="error"
                                    variant="simple"
                                    size="small"
                                >
                                    {{ userForm.errors.username }}
                                </Message>
                            </div>

                            <!-- Password -->
                            <div>
                                <FloatLabel variant="on">
                                    <Password
                                        fluid
                                        toggleMask
                                        :feedback="false"
                                        v-model="userForm.password"
                                        :invalid="!!userForm.errors.password"
                                    />
                                    <label>Password</label>
                                </FloatLabel>
                                <Message
                                    v-if="userForm.errors.password"
                                    severity="error"
                                    variant="simple"
                                    size="small"
                                >
                                    {{ userForm.errors.password }}
                                </Message>
                            </div>

                            <div v-if="formMode == 'edit'">
                                <FloatLabel variant="on">
                                    <Password
                                        fluid
                                        toggleMask
                                        :feedback="false"
                                        v-model="userForm.password_baru"
                                        :invalid="
                                            !!userForm.errors.password_baru
                                        "
                                    />
                                    <label>Password Baru</label>
                                </FloatLabel>
                                <Message
                                    v-if="userForm.errors.password_baru"
                                    severity="error"
                                    variant="simple"
                                    size="small"
                                >
                                    {{ userForm.errors.password_baru }}
                                </Message>
                            </div>

                            <!-- Role -->
                            <div>
                                <FloatLabel variant="on">
                                    <Select
                                        fluid
                                        :options="roleOptions"
                                        v-model="userForm.role"
                                        optionLabel="name"
                                        optionValue="value"
                                        :invalid="!!userForm.errors.role"
                                    />
                                    <label>Role</label>
                                </FloatLabel>
                                <Message
                                    v-if="userForm.errors.role"
                                    severity="error"
                                    variant="simple"
                                    size="small"
                                >
                                    {{ userForm.errors.role }}
                                </Message>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="mt-4 flex justify-end">
                            <Button
                                label="Simpan"
                                icon="pi pi-check-circle"
                                type="submit"
                            />
                        </div>
                    </form>
                </Dialog>

                <Card>
                    <template #header>
                        <div class="flex items-center justify-between p-4">
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
                    <template #content>
                        <DataTable
                            removableSort
                            v-model:filters="filters"
                            ref="dt"
                            :value="dataUser"
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
                                field="username"
                                header="Username"
                            />
                            <Column sortable header="Nama lengkap">
                                <template #body="{ data }">
                                    {{ formatName(data.nama) }}
                                </template>
                            </Column>
                            <Column sortable field="role" header="Role">
                                <template #body="{ data }">
                                    <Tag
                                        v-if="data.role == 'perangkat'"
                                        icon="pi pi-user"
                                        severity="info"
                                        value="Perangkat Kampung"
                                    ></Tag>
                                    <Tag
                                        v-else-if="data.role == 'kepala'"
                                        icon="pi pi-user"
                                        severity="warn"
                                        value="Kepala Kampung"
                                    ></Tag>
                                    <Tag
                                        v-else="data.role == 'warga'"
                                        icon="pi pi-user"
                                        severity="secondary"
                                        value="Warga"
                                    ></Tag>
                                </template>
                            </Column>
                            <Column header="Opsi">
                                <template #body="{ data }">
                                    <div class="flex gap-2 items-center">
                                        <Button
                                            size="small"
                                            @click="EditForm(data)"
                                            icon="pi pi-pen-to-square"
                                            iconPos="right"
                                            severity="info"
                                            outlined
                                        />
                                        <Button
                                            size="small"
                                            @click="confirmDelete(data)"
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
            </div>
        </template>
    </TemplateLayout>
</template>

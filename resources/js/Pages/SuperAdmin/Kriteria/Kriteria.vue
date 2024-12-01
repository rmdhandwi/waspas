<script setup>
import { onMounted, ref } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
// import layout
import Layout from "@/Layouts/TemplateLayout.vue";

// import component
import kriteriaComp from "./Component/KriteriaPage.vue";
import subKriteriaComp from "./Component/SubKriteriaPage.vue";
import {
    Button,
    Dialog,
    InputText,
    InputNumber,
    Select,
    Toast,
    useToast,
    FloatLabel,
    Message,
} from "primevue";

onMounted(() => {
    checkNotif();
});

const props = defineProps({
    dataKriteria: Object,
    dataSubKriteria: Object,
    flash: Object,
    auth: Object,
});


const toast = useToast();

let is_kriteria = props.flash?.is_kriteria || ref(true);
const isLoading = ref(false);

let showKriteriaForm = ref(false);
let showSubKriteriaForm = ref(false);

const checkNotif = () => {
    if (props.flash.notif_status) {
        setTimeout(() => {
            if (props.flash.notif_status === "success") {
                toast.add({
                    severity: "success",
                    summary: "success",
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
    showKriteriaForm.value = false;
    showSubKriteriaForm.value = false;
    isLoading.value = true;
    router.visit(route("super_admin.kriteria"));
    setTimeout(() => (isLoading.value = false), 600);
};

const tambahData = () => {
    is_kriteria.value
        ? (showKriteriaForm.value = true)
        : (showSubKriteriaForm.value = true);
};

let tipe = [{ nama: "Cost" }, { nama: "Benefit" }];

const kriteriaForm = useForm({
    nama: "",
    nilai_bobot: null,
    tipe: null,
});

const subKriteriaForm = useForm({
    nama: null,
    nilai_bobot: null,
    id_relasi: null,
});

const tambahKriteria = () => {
    showKriteriaForm.value = false;

    kriteriaForm.post(route("super_admin.tambah.kriteria"), {
        onSuccess: () => refreshPage(),
        onError: () => {
            toast.add({
                severity: "error",
                summary: "notifikasi",
                detail: "Gagal menambahkan data kriteria :( ",
                life: 4000,
                group: "tc",
            }),
                (showKriteriaForm.value = true);
        },
    });
};
const tambahSubKriteria = () => {
    showSubKriteriaForm.value = false;
    subKriteriaForm.post(route("super_admin.tambah.sub_kriteria"), {
        onSuccess: () => refreshPage(),
        onError: () => {
            toast.add({
                severity: "error",
                summary: "notifikasi",
                detail: "Gagal menambahkan data subkrtieria :( ",
                life: 4000,
                group: "tc",
            }),
                (showSubKriteriaForm.value = true);
        },
    });
};

const errorToast = (errorMessage) => {
    toast.add({
        severity: "error",
        summary: "notifikasi",
        detail: errorMessage,
        life: 4000,
        group: "tc",
    });
};
</script>

<template>
    <Head :title="is_kriteria ? 'Kriteria' : 'Sub Kriteria'" />
    <Layout :auth="auth">
        <template #pageContent>
            <Toast position="top-center" group="tc" />
            <div class="flex flex-col gap-[1rem] p-4">
                <h1 class="text-xl font-semibold">Data Kriteria</h1>
                <div class="flex items-center justify-between">
                    <div class="flex gap-8">
                        <Button
                            @click="is_kriteria = true"
                            label="Kriteria"
                            severity="info"
                            size="small"
                            class="w-[12rem]"
                            :outlined="!is_kriteria"
                        />
                        <Button
                            @click="is_kriteria = false"
                            label="Sub Kriteria"
                            severity="info"
                            size="small"
                            class="w-[12rem]"
                            :outlined="is_kriteria"
                        />
                    </div>

                    <Button
                        @click="tambahData()"
                        label="Tambah Data"
                        size="small"
                        class="w-[12rem]"
                        icon="pi pi-plus-circle"
                    />
                </div>

                <!-- dialog tambah data kriteria -->
                <Dialog
                    modal
                    header="Tambah Kriteria"
                    :style="{ width: '25rem' }"
                    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                    v-model:visible="showKriteriaForm"
                >
                    <form
                        @submit.prevent="tambahKriteria"
                        class="flex flex-col gap-4 mt-1"
                        autocomplete="off"
                    >
                        <!-- Nama Kriteria -->
                        <div class="w-full">
                            <FloatLabel variant="on">
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

                        <!-- Nilai Bobot -->
                        <div class="w-full">
                            <FloatLabel variant="on">
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

                        <!-- Tipe -->
                        <div class="w-full">
                            <FloatLabel variant="on">
                                <Select
                                    fluid
                                    id="tipe"
                                    v-model="kriteriaForm.tipe"
                                    :options="tipe"
                                    optionLabel="nama"
                                    optionValue="nama"
                                    :invalid="!!kriteriaForm.errors.tipe"
                                />
                                <label for="tipe">Type</label>
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

                        <!-- Tombol Aksi -->
                        <div class="flex justify-end gap-2 w-full mt-4">
                            <Button
                                type="button"
                                label="Reset"
                                severity="danger"
                                @click="kriteriaForm.reset()"
                            />
                            <Button
                                type="submit"
                                label="Simpan Data"
                                severity="info"
                            />
                        </div>
                    </form>
                </Dialog>

                <!-- dialog tambah data kriteria selesai -->

                <Dialog
                    modal
                    header="Tambah Sub Kriteria"
                    :style="{ width: '25rem' }"
                    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                    v-model:visible="showSubKriteriaForm"
                >
                    <form
                        @submit.prevent="tambahSubKriteria"
                        class="flex flex-col gap-4 mt-1"
                        autocomplete="off"
                    >
                        <!-- Nama Sub Kriteria -->
                        <div class="w-full">
                            <FloatLabel variant="on">
                                <InputText
                                    fluid
                                    v-model="subKriteriaForm.nama"
                                    id="nama"
                                    autocomplete="off"
                                    :invalid="!!subKriteriaForm.errors.nama"
                                />
                                <label for="nama">Nama Sub Kriteria</label>
                            </FloatLabel>
                            <Message
                                v-if="subKriteriaForm.errors.nama"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ subKriteriaForm.errors.nama }}
                            </Message>
                        </div>

                        <!-- Nilai Bobot -->
                        <div class="w-full">
                            <FloatLabel variant="on">
                                <InputNumber
                                    fluid
                                    v-model="subKriteriaForm.nilai_bobot"
                                    inputId="nilai"
                                    mode="decimal"
                                    showButtons
                                    :min="0"
                                    :max="100"
                                    :invalid="
                                        !!subKriteriaForm.errors.nilai_bobot
                                    "
                                />
                                <label for="nilai">Nilai Bobot (%)</label>
                            </FloatLabel>
                            <Message
                                v-if="subKriteriaForm.errors.nilai_bobot"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ subKriteriaForm.errors.nilai_bobot }}
                            </Message>
                        </div>

                        <!-- ID Kriteria -->
                        <div class="w-full">
                            <FloatLabel variant="on">
                                <Select
                                    fluid
                                    v-model="subKriteriaForm.id_relasi"
                                    :options="props.dataKriteria"
                                    optionLabel="kode_kriteria"
                                    optionValue="id"
                                    :invalid="
                                        !!subKriteriaForm.errors.id_relasi
                                    "
                                />
                                <label for="id_relasi">ID Kriteria</label>
                            </FloatLabel>
                            <Message
                                v-if="subKriteriaForm.errors.id_relasi"
                                severity="error"
                                size="small"
                                variant="simple"
                            >
                                {{ subKriteriaForm.errors.id_relasi }}
                            </Message>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex justify-end gap-2 w-full mt-4">
                            <Button
                                type="button"
                                label="Reset"
                                severity="danger"
                                @click="subKriteriaForm.reset()"
                            />
                            <Button
                                type="submit"
                                label="Simpan Data"
                                severity="info"
                            />
                        </div>
                    </form>
                </Dialog>

                <kriteriaComp
                    v-if="is_kriteria"
                    :dataKriteria="props.dataKriteria"
                    @refresh-page="refreshPage()"
                />
                <subKriteriaComp
                    v-else
                    :dataKriteria="props.dataKriteria"
                    :dataSubKriteria="dataSubKriteria"
                    @refresh-page="refreshPage()"
                    @error-toast="
                        errorToast('Gagal update data sub kriteria :( ')
                    "
                />
            </div>
        </template>
    </Layout>
</template>

<style scoped></style>

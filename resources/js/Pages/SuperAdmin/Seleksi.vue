<script setup>
import { onMounted, ref, defineProps, computed } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import Layout from "@/Layouts/TemplateLayout.vue";
import {
    DataTable,
    Column,
    Button,
    Dialog,
    Select,
    Toast,
    FloatLabel,
    Message,
    useToast,
} from "primevue";
import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    warga: Array,
    subkriteria: Array,
    seleksi: Object,
    flash: Object,
    auth: Object,
    column: Array,
});

let groupedSubkriteria = ref({});
let dataseleksi = ref([]);
let showForm = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
let dt = ref();

const seleksiForm = useForm({
    warga: null,
    C1: null,
    C2: null,
    C3: null,
    C4: null,
    C5: null,
    C6: null,
    C7: null,
    C8: null,
});

const toast = useToast();

onMounted(() => {
    dataseleksi.value = props.seleksi.map((p, i) => ({
        index: i + 1,
        ...p,
    }));
    checkNotif();

    // Group subkriteria by kriteria
    props.subkriteria.forEach((sub) => {
        const kriteriajenis = sub.kriteria.jenis;
        if (!groupedSubkriteria.value[kriteriajenis]) {
            groupedSubkriteria.value[kriteriajenis] = {
                kriteria: sub.kriteria.nama,
                subkriteria: [],
            };
        }
        groupedSubkriteria.value[kriteriajenis].subkriteria.push(sub);
    });

    console.log("Grouped Subkriteria:", groupedSubkriteria.value);
});

// Computed options for dynamic selects based on criteria
const dynamicOptions = computed(() => {
    const options = {};
    for (const [key, value] of Object.entries(groupedSubkriteria.value)) {
        if (value && Array.isArray(value.subkriteria)) {
            options[key] = {
                kriteria: value.kriteria,
                subkriteria: value.subkriteria.map((sub) => ({
                    id: sub.id,
                    label: sub.nama_sub,
                    value: sub.nilai_bobot,
                })),
            };
        } else {
            options[key] = { kriteria: "Kriteria", subkriteria: [] };
        }
    }
    return options;
});

// Export table data to CSV
const exportCSV = () => {
    dt.value.exportCSV();
};

// Notification handling
const checkNotif = () => {
    if (props.flash.notif_status) {
        setTimeout(() => {
            toast.add({
                severity:
                    props.flash.notif_status === "success"
                        ? "success"
                        : "error",
                summary: "Info",
                detail: props.flash.notif_message,
                life: 4000,
                group: "tc",
            });
        }, 1000);
    }
};

// Refresh page and reset form
const refreshPage = () => {
    checkNotif();
    showForm.value = false;
    router.visit(route("seleksi"));
    seleksiForm.reset();
};

// Form submission handler
const submitData = () => {
    seleksiForm.value = false;

    seleksiForm.post(route("tambah_seleksi"), {
        onSuccess: () => refreshPage(),
        onError: () => {
            toast.add({
                severity: "error",
                summary: "notifikasi",
                detail: "Gagal menambahkan data kriteria :( ",
                life: 4000,
                group: "tc",
            }),
                (seleksiForm.value = true);
        },
    });
};

const handleWargaChange = (selectedWargaId) => {
    const selectedWarga = props.warga.find((w) => w.id === selectedWargaId);

    if (selectedWarga) {
        // Log the selected warga data
        console.log("Selected Warga Data:", selectedWarga);

        // Compare each relevant field from 'warga' with the 'subkriteria'
        updateSelectBasedOnWargaData("C1", selectedWarga.jmlh_penghasilan);
        updateSelectBasedOnWargaData("C2", selectedWarga.status_rumah);
        updateSelectBasedOnWargaData("C3", selectedWarga.struktur_bangunan);
        updateSelectBasedOnWargaData("C4", selectedWarga.lahan);
        updateSelectBasedOnWargaData("C5", selectedWarga.legalitas_lahan);
        updateSelectBasedOnWargaData("C6", selectedWarga.jmlh_keluarga, true);
        updateSelectBasedOnWargaData("C7", selectedWarga.sanitasi);
        updateSelectBasedOnWargaData("C8", selectedWarga.t_limbah);

        // Log the updated form after processing all criteria
        console.log("Updated Form Data after Comparison:", seleksiForm);
    }
};

// Function to update the Select field based on comparison with subkriteria
const updateSelectBasedOnWargaData = (
    criteriaKey,
    wargaValue,
    isFamilySize = false
) => {
    if (wargaValue !== null && wargaValue !== undefined) {
        let normalizedValue = String(wargaValue)
            .toLowerCase()
            .replace(/\s+/g, "");

        // Special handling for family size (C6)
        if (isFamilySize) {
            if (wargaValue <= 2) normalizedValue = "<=2orang";
            else if (wargaValue >= 3 && wargaValue <= 5)
                normalizedValue = "3-5orang";
            else normalizedValue = ">6orang";
        }

        // Log the normalized value for debugging
        console.log(
            `Criteria: ${criteriaKey}, Normalized Value: ${normalizedValue}`
        );

        // Find matching subkriteria based on the normalized value
        const matchedSub = props.subkriteria.find(
            (sub) =>
                sub.nama_sub.toLowerCase().replace(/\s+/g, "") ===
                normalizedValue
        );

        // If a match is found, update the corresponding criteria in seleksiForm
        if (matchedSub) {
            seleksiForm[criteriaKey] = matchedSub.nilai_bobot;
            console.log(`Matched Subkriteria for ${criteriaKey}:`, matchedSub);
        } else {
            seleksiForm[criteriaKey] = null; // Reset if no match found
            console.log(`No matching Subkriteria found for ${criteriaKey}`);
        }
    }
};
</script>

<template>
    <Head title="Seleksi" />
    <Layout :auth="auth">
        <template #pageContent>
            <div class="flex flex-col gap-4">
                <Toast position="top-center" group="tc" />
                <div class="p-4 flex justify-between items-center">
                    <h1 class="text-lg">Daftar Seleksi</h1>
                    <Button
                        icon="pi pi-plus-circle"
                        size="small"
                        label="Tambah Data"
                        @click="showForm = true"
                    />
                </div>

                <Dialog
                    v-model:visible="showForm"
                    modal
                    :style="{ width: '50vw' }"
                    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                    header="Tambah Data Seleksi"
                >
                    <form
                        @submit.prevent="submitData"
                        class="flex flex-col gap-y-4"
                        autocomplete="off"
                    >
                        <div class="flex items-center flex-wrap gap-4 my-4">
                            <div class="warga w-[14.4rem]">
                                <FloatLabel variant="on" class="w-full">
                                    <Select
                                        fluid
                                        class="text-slate-950"
                                        id="select_warga"
                                        v-model="seleksiForm.warga"
                                        :options="warga"
                                        optionValue="id"
                                        optionLabel="nama_kk"
                                        :@keyup="console.log(seleksiForm.warga)"
                                        @change="
                                            handleWargaChange(seleksiForm.warga)
                                        "
                                        :invalid="!!seleksiForm.errors.warga"
                                    />
                                    <label for="select_warga"
                                        >Pilih Warga</label
                                    >
                                </FloatLabel>
                                <Message
                                    v-if="seleksiForm.errors.warga"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ seleksiForm.errors.warga }}
                                </Message>
                            </div>

                            <div
                                v-for="(
                                    optionGroup, criteria
                                ) in dynamicOptions"
                                :key="criteria"
                                class="warga w-[14.4rem]"
                            >
                                <FloatLabel variant="on" class="w-full">
                                    <Select
                                        fluid
                                        class="text-slate-950"
                                        :id="`select_${criteria}`"
                                        v-model="seleksiForm[criteria]"
                                        :options="optionGroup.subkriteria"
                                        optionValue="value"
                                        optionLabel="label"
                                        :@keyup="
                                            console.log(seleksiForm[criteria])
                                        "
                                        :invalid="
                                            !!seleksiForm.errors[criteria]
                                        "
                                    />
                                    <label :for="`select_${criteria}`">{{
                                        optionGroup.kriteria
                                    }}</label>
                                </FloatLabel>
                                <Message
                                    v-if="seleksiForm.errors[criteria]"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ seleksiForm.errors[criteria] }}
                                </Message>
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <Button
                                label="Batal"
                                size="small"
                                severity="danger"
                                text
                                @click="showForm = false"
                            />
                            <Button
                                label="Simpan"
                                size="small"
                                type="submit"
                                class="ml-2"
                            />
                        </div>
                    </form>
                </Dialog>

                <!-- Dynamic DataTable -->
                <DataTable
                    :filters="filters"
                    :value="dataseleksi"
                    stripedRows
                    showGridlines
                    class="p-5"
                    ref="dt"
                    responsiveLayout="scroll"
                    paginator
                    :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    paginatorPosition="both"
                >
                    <!-- Render Warga's nama_kk and nomor_kk explicitly -->
                    <Column field="index" header="No" style="min-width: 1rem" />
                    <Column
                        field="warga.nama_kk"
                        header="Nama KK"
                        style="min-width: 12rem"
                    />
                    <Column
                        field="warga.nomor_kk"
                        header="Nomor KK"
                        style="min-width: 12rem"
                    />

                    <!-- Dynamically render the remaining columns, skipping the first 3 columns -->
                    <Column
                        v-for="(column, index) in props.column"
                        :key="index"
                        :field="`${column}`"
                        :header="column"
                        style="min-width: 12rem"
                    />
                </DataTable>
            </div>
        </template>
    </Layout>
</template>

<script></script>

<style scoped></style>

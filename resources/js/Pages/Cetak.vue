<script setup>
import { onMounted, defineProps } from "vue";

const props = defineProps({
    tahun: String, // Tahun periode
    tgl: String, // Tanggal dibuat
    oleh: String, // Dibuat oleh
    setuju: String, // Disetujui oleh
    hasil: Array, // Data hasil
});

// Fungsi untuk mencetak halaman
onMounted(() => {
    window.print();
});

const formatName = (columnName) => {
    return columnName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

// Fungsi untuk memformat tanggal
const formatTanggal = (tanggal) => {
    const date = new Date(tanggal);
    const options = { day: "numeric", month: "long", year: "numeric" };
    return new Intl.DateTimeFormat("id-ID", options).format(date);
};
</script>

<template>
    <div class="print-container">
        <!-- Header -->
        <div
            class="header flex flex-col justify-center items-center text-center mb-6"
        >
            <!-- Logo -->
            <img src="logo.png" alt="Logo" class="logo-image" />
            <h1 class="text-2xl font-bold">Laporan Hasil Akhir</h1>
            <p class="text-lg">Periode Tahun : {{ tahun }}</p>
        </div>

        <!-- Informasi -->
        <div class="info mb-6 w-[25rem] p-4 rounded-lg">
            <div class="flex items-center mb-2">
                <strong class="w-[40%] text-left">Tanggal Dibuat</strong>
                <span class="w-[10%] text-center">:</span>
                <span class="w-[50%] text-left">{{ formatTanggal(tgl) }}</span>
            </div>
            <div class="flex items-center mb-2">
                <strong class="w-[40%] text-left">Dibuat Oleh</strong>
                <span class="w-[10%] text-center">:</span>
                <span class="w-[50%] text-left">{{ oleh }}</span>
            </div>
            <div class="flex items-center">
                <strong class="w-[40%] text-left">Disetujui Oleh</strong>
                <span class="w-[10%] text-center">:</span>
                <span class="w-[50%] text-left">{{ setuju }}</span>
            </div>
        </div>

        <!-- Tabel Hasil -->
        <table
            class="table-auto w-full border-collapse border border-gray-300 mb-6"
        >
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">No</th>
                    <th class="border border-gray-300 px-4 py-2">Nama Warga</th>
                    <th class="border border-gray-300 px-4 py-2">Periode</th>
                    <th class="border border-gray-300 px-4 py-2">
                        Hasil Akhir
                    </th>
                    <th class="border border-gray-300 px-4 py-2">Rank</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(item, index) in props.hasil"
                    :key="index"
                    class="hover:bg-gray-100"
                >
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        {{ index + 1 }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        {{ formatName(item.warga.nama_kk) }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        {{ item.warga.periode.tahun }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        {{ item.skor_akhir }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        {{ item.peringkat }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="ttd-container page-break-inside-avoid">
            <div class="ttd flex justify-end mt-12">
                <div class="text-center">
                    <p>Kepala Kampung Sereh</p>
                    <br /><br /><br />
                    <p>_________________________</p>
                    <p>Steven Eluay, S.E</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.print-container {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 20px;
    padding: 20px;
    background-color: white;
    color: black;
}

/* Logo styling */
.logo-image {
    width: 60px;
    height: auto;
}

/* Garis putus-putus untuk page break */
.page-break-inside-avoid {
    break-inside: avoid;
    page-break-inside: avoid;
}
</style>

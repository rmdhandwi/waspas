<script setup>
import { defineProps } from "vue";
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/TemplateLayout.vue";
import { Card, Button } from "primevue";

const props = defineProps({
    countKriteria: Number,
    countUsers: Number,
    countSub: Number,
    countWarga: Number,
    countHasil: Number,
    wargaPerPeriode: Array,
    wargaStatus: Object,
    auth: Object,
});

// Data untuk Chart Periode
const periodeChartData = {
    labels: props.wargaPerPeriode.map((item) => item.tahun),
    datasets: [
        {
            label: "Jumlah Warga",
            data: props.wargaPerPeriode.map((item) => item.total),
            backgroundColor: "rgba(66, 165, 245, 0.4)", // Opasitas rendah
            borderColor: "rgb(66, 165, 245)", // Outline warna terang
            borderWidth: 1, // Lebar border (outline)
            borderRadius: 4, // Sudut yang lebih lembut
            animation: {
                duration: 1500, // Animasi pada dataset ini
                easing: "easeOutBounce", // Gaya animasi khusus untuk dataset ini
            },
        },
    ],
};

// Data untuk Chart Status Penerimaan Per Periode
const statusChartData = {
    labels: props.wargaPerPeriode.map((periode) => periode.tahun),
    datasets: [
        {
            label: "Sudah Menerima",
            backgroundColor: "rgba(102, 187, 106, 0.4)",
            borderColor: "rgb(102, 187, 106)",
            borderWidth: 1,
            data: props.wargaPerPeriode.map(
                (periode) => props.wargaStatus[periode.tahun]?.received || 0
            ),
            animation: {
                duration: 1500, // Animasi pada dataset ini
                easing: "easeOutBounce", // Gaya animasi khusus untuk dataset ini
            },
        },
        {
            label: "Belum Menerima",
            backgroundColor: "rgba(255, 167, 38, 0.4)",
            borderColor: "rgb(255, 167, 38)",
            borderWidth: 1,
            data: props.wargaPerPeriode.map(
                (periode) => props.wargaStatus[periode.tahun]?.notReceived || 0
            ),
            animation: {
                duration: 1500, // Animasi pada dataset ini
                easing: "easeOutBounce", // Gaya animasi khusus untuk dataset ini
            },
        },
    ],
};

// Opsi Chart
const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor =
        documentStyle.getPropertyValue("--p-text-color") || "#000000"; // Default jika CSS tidak diatur
    const textColorSecondary =
        documentStyle.getPropertyValue("--p-text-muted-color") || "#6b7280";
    const surfaceBorder =
        documentStyle.getPropertyValue("--p-content-border-color") || "#e5e7eb";

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: textColor,
                    font: {
                        size: 14,
                        family: "Arial, sans-serif",
                    },
                },
            },
            tooltip: {
                backgroundColor: "rgba(0, 0, 0, 0.8)",
                titleColor: "#ffffff",
                bodyColor: "#ffffff",
                borderWidth: 1,
                borderColor: surfaceBorder,
                cornerRadius: 6,
            },
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary,
                    font: {
                        size: 12,
                    },
                },
                grid: {
                    color: surfaceBorder,
                },
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: textColorSecondary,
                    font: {
                        size: 12,
                    },
                },
                grid: {
                    color: surfaceBorder,
                },
            },
        },
    };
};
</script>

<template>
    <Head title="Dashboard" />
    <Layout :auth="props.auth">
        <template #pageContent>
            <div
                :class="{
                    'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4':
                        props.auth.user.role === 'kepala',
                    'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4':
                        props.auth.user.role === 'perangkat',
                }"
            >
                <!-- Cards for Super Admin -->
                <template v-if="props.auth.user.role === 'perangkat'">
                    <!-- Card: Users -->
                    <Button
                        as="a"
                        :href="route('super_admin.pengguna')"
                        unstyled
                        class="shake"
                    >
                        <Card
                            class="p-6 rounded-md shadow-md bg-blue-400"
                            unstyled
                        >
                            <template #content>
                                <div
                                    class="flex justify-between items-center text-white"
                                >
                                    <i
                                        class="pi pi-users"
                                        style="font-size: 2rem"
                                    ></i>
                                    <div class="flex flex-col items-center">
                                        <h1>Users</h1>
                                        <h1>{{ props.countUsers }}</h1>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </Button>

                    <!-- Card: Kriteria -->
                    <Button
                        as="a"
                        :href="route('super_admin.kriteria')"
                        unstyled
                        class="shake"
                    >
                        <Card
                            class="p-6 rounded-md shadow-md bg-green-400"
                            unstyled
                        >
                            <template #content>
                                <div
                                    class="flex justify-between items-center text-white"
                                >
                                    <i
                                        class="pi pi-tag"
                                        style="font-size: 2rem"
                                    ></i>
                                    <div class="flex flex-col items-center">
                                        <h1>Kriteria</h1>
                                        <h1>{{ props.countKriteria }}</h1>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </Button>

                    <!-- Card: Subkriteria -->
                    <Button
                        as="a"
                        :href="route('super_admin.kriteria')"
                        unstyled
                        class="shake"
                    >
                        <Card
                            class="p-6 rounded-md shadow-md bg-purple-400"
                            unstyled
                        >
                            <template #content>
                                <div
                                    class="flex justify-between items-center text-white"
                                >
                                    <i
                                        class="pi pi-tags"
                                        style="font-size: 2rem"
                                    ></i>
                                    <div class="flex flex-col items-center">
                                        <h1>Subkriteria</h1>
                                        <h1>{{ props.countSub }}</h1>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </Button>
                </template>

                <!-- Common Cards (Admin and Super Admin) -->
                <template
                    v-if="
                        props.auth.user.role === 'kepala' ||
                        props.auth.user.role === 'perangkat'
                    "
                >
                    <!-- Card: Warga -->
                    <Button
                        as="a"
                        :href="route('wargapage')"
                        unstyled
                        class="shake"
                    >
                        <Card
                            class="p-6 rounded-md shadow-md bg-red-400"
                            unstyled
                        >
                            <template #content>
                                <div
                                    class="flex justify-between items-center text-white"
                                >
                                    <i
                                        class="pi pi-users"
                                        style="font-size: 2rem"
                                    ></i>
                                    <div class="flex flex-col items-center">
                                        <h1>Warga</h1>
                                        <h1>{{ props.countWarga }}</h1>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </Button>

                    <!-- Card: Hasil -->
                    <Button
                        as="a"
                        :href="route('hasil')"
                        unstyled
                        class="shake"
                    >
                        <Card
                            class="p-6 rounded-md shadow-md bg-orange-400"
                            unstyled
                        >
                            <template #content>
                                <div
                                    class="flex justify-between items-center text-white"
                                >
                                    <i
                                        class="pi pi-chart-bar"
                                        style="font-size: 2rem"
                                    ></i>
                                    <div class="flex flex-col items-center">
                                        <h1>Hasil</h1>
                                        <h1>{{ props.countHasil }}</h1>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </Button>
                </template>
            </div>

            <!-- Chart Section -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Chart: Warga Per Periode -->
                <Card class="p-6 rounded-md shadow-md">
                    <template #title>
                        <h1 class="text-lg font-semibold mb-4">
                            Warga Per Periode
                        </h1>
                    </template>
                    <template #content>
                        <Chart
                            type="bar"
                            :data="periodeChartData"
                            :options="setChartOptions"
                        />
                    </template>
                </Card>

                <!-- Status Penerimaan Per Periode -->
                <Card class="p-6 rounded-md shadow-md">
                    <template #title>
                        <h1 class="text-lg font-bold mb-4">
                            Status Penerimaan Per Periode
                        </h1>
                    </template>
                    <template #content>
                        <!-- Chart: Status Penerimaan Per Periode -->
                        <Chart
                            type="bar"
                            :data="statusChartData"
                            :options="setChartOptions"
                        />
                    </template>
                </Card>
            </div>
        </template>
    </Layout>
</template>

<style scoped>
@keyframes shake {
    0%,
    100% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-4px);
    }
    50% {
        transform: translateX(4px);
    }
    75% {
        transform: translateX(-4px);
    }
}

.shake:hover {
    animation: shake 0.4s ease-in-out;
}
</style>

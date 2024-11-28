<script setup>
import { Head } from "@inertiajs/vue3";
// import layout
import Layout from "@/Layouts/TemplateLayout.vue";
import { Card, Button } from "primevue";

// Props and attributes setup
const pageProps = defineProps({
    countKriteria: Number,
    countUsers: Number,
    flash: Object,
    auth: Object,
});

const auth = pageProps.auth;
</script>

<template>
    <Head title="Dashboard" />
    <Layout :auth="auth">
        <template #pageContent>
            <div class="flex w-full gap-x-[2rem] items-center">
                <!-- Card for Kriteria -->
                <Button as="a" :href="route('super_admin.kriteria')" unstyled>
                    <Card
                        class="w-[18rem] p-6 rounded-md shadow-md bg-green-400"
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
                                    <h1>Kriteria</h1>
                                    <h1>{{ pageProps.countKriteria }}</h1>
                                </div>
                            </div>
                        </template>
                    </Card>
                </Button>

                <!-- Card for Users -->
                <Button as="a" :href="route('super_admin.kriteria')" unstyled>
                    <Card
                        class="w-[18rem] p-6 rounded-md shadow-md bg-blue-400"
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
                                    <h1>{{ pageProps.countUsers }}</h1>
                                </div>
                            </div>
                        </template>
                    </Card>
                </Button>
            </div>
        </template>
    </Layout>
</template>

<style scoped></style>

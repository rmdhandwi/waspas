<script setup>
import { onMounted, useAttrs } from "vue";
import { Head, router } from "@inertiajs/vue3";
// import layout
import Layout from "@/Layouts/TemplateLayout.vue";
import { Button, Card } from "primevue";

// const pageAttrs = useAttrs();
const pageProps = defineProps({
    countDataWarga: Number,
    auth: Object,
});

const auth = pageProps.auth;

</script>

<template>
    <Head title="Dashboard" />
    <Layout :auth="auth">
        <template #pageContent>
            <div class="flex w-full gap-x-[2rem] items-center">
                <Button as="a" :href="route('admin.data_warga')" unstyled>
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
                                    <h1>Data Warga</h1>
                                    <h1>{{ pageProps.countDataWarga }}</h1>
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

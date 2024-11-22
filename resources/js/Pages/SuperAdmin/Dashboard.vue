<script setup>
import { Head, router } from '@inertiajs/vue3'
// import layout
import Layout from '@/Layouts/TemplateLayout.vue'
import { onMounted , useAttrs } from 'vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
const pageAttrs = useAttrs()

const pageProps = defineProps({
    countKriteria : Number,
    countUsers : Number,
})

onMounted(() =>
{
    localStorage.setItem('username', pageAttrs.auth.user.username)
    localStorage.setItem('role', pageAttrs.auth.user.role)
})
</script>

<template>
    <Head title="Dashboard"/>
    <Layout>
        <template #pageContent>
            <div class="flex w-full gap-x-[2rem] items-center">
                <Button as="a" :href="route('super_admin.kriteria')" unstyled>
                    <Card class="w-[18rem] p-6 rounded-md shadow-md bg-green-400" unstyled>
                        <template #content>
                            <div class="flex justify-between items-center text-white">
                                <i class="pi pi-users" style="font-size: 2rem;"></i>
                                <div class="flex flex-col items-center">
                                    <h1>Kriteria</h1>
                                    <h1>{{ pageProps.countKriteria }}</h1>
                                </div>
                            </div>
                        </template>
                    </Card>
                </Button>

                <Button as="a" :href="route('super_admin.kriteria')" unstyled>
                    <Card class="w-[18rem] p-6 rounded-md shadow-md bg-blue-400" unstyled>
                        <template #content>
                            <div class="flex justify-between items-center text-white">
                                <i class="pi pi-users" style="font-size: 2rem;"></i>
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

<style scoped>
</style>
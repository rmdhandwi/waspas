<script setup>
import { onMounted , useAttrs } from 'vue'
import { Head } from '@inertiajs/vue3'
// import layout
import Layout from '@/Layouts/TemplateLayout.vue'

import Button from 'primevue/button';
import Card from 'primevue/card';

const pageAttrs = useAttrs()

const pageProps = defineProps({ countDataWarga : Number })

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
                <Button as="a" :href="route('admin.data_warga')" unstyled>
                    <Card class="w-[18rem] p-6 rounded-md shadow-md bg-green-400" unstyled>
                        <template #content>
                            <div class="flex justify-between items-center text-white">
                                <i class="pi pi-users" style="font-size: 2rem;"></i>
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

<style scoped>
</style>
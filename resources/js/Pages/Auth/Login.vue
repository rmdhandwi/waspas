<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { onMounted, ref } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import {
    Toast,
    useToast,
    Password,
    Button,
    InputText,
    Dialog,
    FloatLabel,
    Message,
    IconField
} from "primevue";

let showDialog = ref(false);

const registerShow = () => {
    formRegister.reset();
    formRegister.clearErrors();
    showDialog.value = true;
};

onMounted(() => {
    checkNotif();
});

const props = defineProps({
    flash: Object,
});

const toast = useToast();

const checkNotif = () => {
    if (props.flash.notif_status) {
        setTimeout(() => {
            if (props.flash.notif_status === "success") {
                toast.add({
                    severity: "success",
                    summary: "Info",
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

const isLoading = ref(false);

const refreshPage = () => {
    checkNotif();
    isLoading.value = true;
    router.visit(route("login"));
    setTimeout(() => (isLoading.value = false), 600);
};

const form = useForm({
    username: "",
    password: "",
});

const formRegister = useForm({
    nama: "",
    username: "",
    password: "",
});

const register = () => {
    formRegister.post(route("registerSubmit"), {
        onSuccess: () => refreshPage(),
    });
};

const submit = () => {
    form.post(route("loginSubmit"), {
        onSuccess: () => refreshPage(),
    });
};
</script>

<template>
    <Head title="Log in" />
    <Toast position="top-center" group="tc" />

    <!-- Background with gradient -->
    <div
        class="gradient-background min-h-screen flex flex-col items-center justify-center"
    >
        <!-- Logo and Title -->
        <div class="flex justify-center mb-4">
            <img src="logo.png" alt="Logo" class="h-[10rem]" />
        </div>
        <h2 class="text-center text-white text-xl font-bold mb-4">
            Selamat Datang Kembali
        </h2>

        <div
            class="card-container p-8 bg-white bg-opacity-60 rounded-lg shadow-lg w-full max-w-md"
        >
            <!-- Login Form -->
            <form @submit.prevent="submit" class="space-y-4">
                <!-- Username Field -->
                <div>
                    <FloatLabel variant="in">
                        <InputText
                            id="username"
                            type="text"
                            class="mt-1 w-full"
                            v-model="form.username"
                            autofocus
                        />
                        <label for="username">Username</label>
                    </FloatLabel>
                    <Message
                        v-if="form.errors.username"
                        severity="error"
                        variant="simple"
                        size="small"
                    >
                        {{ form.errors.username }}
                    </Message>
                </div>

                <!-- Password Field -->
                <div>
                    <FloatLabel variant="in">
                        <Password
                            fluid
                            id="password"
                            v-model="form.password"
                            :feedback="false"
                            autocomplete="current-password"
                            toggleMask
                            class="mt-1"
                        />
                        <label for="password">Password</label>
                    </FloatLabel>

                    <Message
                        v-if="form.errors.password"
                        severity="error"
                        variant="simple"
                        size="small"
                    >
                        {{ form.errors.password }}
                    </Message>
                </div>

                <!-- Register Link -->
                <div class="flex justify-end mt-2">
                    <Button
                        as="a"
                        class="hover:text-blue-600 text-sm transition  flex items-center"
                        unstyled
                        :href="route('hasilWarga')"
                    >
                    Hasil seleksi
                    <i class="pi pi-clipboard ms-1"></i>
                    </Button>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex justify-center">
                    <Button
                        class="w-full py-3 bg-blue-500 text-white rounded-md shadow-md hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 disabled:opacity-50"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        unstyled
                        type="submit"
                    >
                        Masuk
                    </Button>
                </div>
            </form>
        </div>
    </div>

    <!-- Registration Dialog -->
    <Dialog
        v-model:visible="showDialog"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        modal
        header="Daftar Akun"
        :style="{ width: '25rem' }"
    >
        <form @submit.prevent="register" autocomplete="off">
            <div class="grid grid-cols-1 gap-4 mt-2">
                <!-- Nama -->
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            fluid
                            id="nama"
                            v-model="formRegister.nama"
                            :invalid="!!formRegister.errors.nama"
                        />
                        <label for="nama">Nama Lengkap</label>
                    </FloatLabel>
                    <Message
                        v-if="formRegister.errors.nama"
                        severity="error"
                        variant="simple"
                        size="small"
                    >
                        {{ formRegister.errors.nama }}
                    </Message>
                </div>

                <!-- Username -->
                <div>
                    <FloatLabel variant="on">
                        <InputText
                            fluid
                            id="username"
                            v-model="formRegister.username"
                            :invalid="!!formRegister.errors.username"
                        />
                        <label for="username">Username</label>
                    </FloatLabel>
                    <Message
                        v-if="formRegister.errors.username"
                        severity="error"
                        variant="simple"
                        size="small"
                    >
                        {{ formRegister.errors.username }}
                    </Message>
                </div>

                <!-- Password -->
                <div>
                    <FloatLabel variant="on">
                        <Password
                            fluid
                            toggleMask
                            :feedback="false"
                            v-model="formRegister.password"
                            :invalid="!!formRegister.errors.password"
                        />
                        <label>Password</label>
                    </FloatLabel>
                    <Message
                        v-if="formRegister.errors.password"
                        severity="error"
                        variant="simple"
                        size="small"
                    >
                        {{ formRegister.errors.password }}
                    </Message>
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-4 flex justify-end">
                <Button
                    label="Daftar"
                    icon="pi pi-check-circle"
                    type="submit"
                />
            </div>
        </form>
    </Dialog>
</template>

<style scoped>
/* Gradient background */
.gradient-background {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
}

/* Card container with transparency */
.card-container {
    background-color: rgba(255, 255, 255, 0.8);
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}
</style>

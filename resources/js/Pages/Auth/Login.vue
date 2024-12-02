<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
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
    <GuestLayout>
        <Head title="Log in" />
        <Toast position="top-center" group="tc" />

        <form @submit.prevent="submit" class="p-4">
            <!-- Username Field -->
            <div>
                <InputLabel for="username" value="Username" />
                <InputText
                    id="username"
                    type="text"
                    class="mt-1 w-full"
                    v-model="form.username"
                    autofocus
                />
                <InputError
                    class="mt-2 text-sm text-red-600"
                    :message="form.errors.username"
                />
            </div>

            <!-- Password Field -->
            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <Password
                    fluid
                    id="password"
                    v-model="form.password"
                    :feedback="false"
                    autocomplete="current-password"
                    toggleMask
                    class="mt-1"
                />
                <InputError
                    class="mt-2 text-sm text-red-600"
                    :message="form.errors.password"
                />
            </div>

            <div class="flex justify-end mt-2">
                <Button
                    @click="registerShow"
                    class="hover:text-blue-600 text-sm transition underline"
                    unstyled
                >
                    Daftar Sekarang?
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

        <Dialog
            v-model:visible="showDialog"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            header="Daftar Akun"
            :style="{ width: '25rem' }"
        >
            <form
                @submit.prevent="register"
                autocomplete="off"
            >
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
    </GuestLayout>
</template>

<style scoped>
/* Custom styling for spacing and focus */
</style>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { onMounted, ref } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { Toast, useToast, Password, Button, InputText } from "primevue";

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
    </GuestLayout>
</template>

<style scoped>
/* Custom styling for spacing and focus */
</style>

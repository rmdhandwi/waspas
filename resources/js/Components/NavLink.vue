<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? "inline-flex items-center gap-4 py-1 font-medium leading-5 text-blue-400 font-wight-semibold border-b-2 border-blue-500 focus:border-blue-400 transition duration-150 ease-in-out"
        : "inline-flex items-center gap-4  font-medium leading-5 hover:text-blue-400 focus:text-blue-500 text-gray-500 transition duration-150 ease-in-out"
);
</script>

<template>
    <Link :href="href" :class="classes" class="outline-none">
        <slot />
    </Link>
</template>

<style scoped>
.active {
    color: blue !important;
}
</style>

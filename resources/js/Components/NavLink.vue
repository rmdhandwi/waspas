<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

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
    props.active ? 
    'inline-flex items-center gap-4 py-1 font-medium leading-5 text-indigo-600 border-b-2 border-indigo-600 focus:border-indigo-400 transition duration-150 ease-in-out' : 
    'inline-flex items-center gap-4  font-medium leading-5 hover:text-indigo-400 focus:text-indigo-600 text-gray-500 transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes" class="outline-none">
        <slot />
    </Link>
</template>

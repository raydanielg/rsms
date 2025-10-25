<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'info' },
    role: { type: String, default: 'alert' },
    title: { type: String, default: '' },
});

const classes = computed(() => {
    const base = 'p-4 mb-4 text-sm rounded-lg';
    const map = {
        info: 'text-blue-800 bg-blue-50 dark:bg-gray-800 dark:text-blue-400',
        danger: 'text-red-800 bg-red-50 dark:bg-gray-800 dark:text-red-400',
        success: 'text-green-800 bg-green-50 dark:bg-gray-800 dark:text-green-400',
        warning: 'text-yellow-800 bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300',
        dark: 'text-gray-800 bg-gray-50 dark:bg-gray-800 dark:text-gray-300',
    };
    return `${base} ${map[props.variant] ?? map.info}`;
});
</script>

<template>
    <div :class="classes" :role="role">
        <span v-if="title" class="font-medium">{{ title }}</span>
        <slot />
    </div>
</template>

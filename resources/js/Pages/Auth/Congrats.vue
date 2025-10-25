<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const show = ref(false);
const DURATION_MS = 2600;

onMounted(() => {
    setTimeout(() => (show.value = true), 40);
    setTimeout(() => router.visit(route('dashboard')), DURATION_MS + 200);
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Registration Complete" />

        <!-- Top-right toast notification -->
        <div class="fixed right-4 top-4 z-50 flex w-full max-w-sm items-start gap-3 rounded-xl bg-white p-4 shadow-xl ring-1 ring-black/5 transition-all duration-300 sm:right-6 sm:top-6"
             :class="show ? 'translate-y-0 opacity-100' : '-translate-y-3 opacity-0'">
            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 4L12 14.01l-3-3"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-900">Account created successfully</p>
                <p class="text-xs text-gray-600">Redirecting to your dashboard...</p>
                <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full bg-green-500" :style="{ animation: `shrink ${DURATION_MS}ms linear forwards` }"></div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes shrink { from { width: 100%; } to { width: 0%; } }
</style>

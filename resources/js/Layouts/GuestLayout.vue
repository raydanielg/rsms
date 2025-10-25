<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0">
        <div
            class="pointer-events-none absolute inset-0 -z-10 bg-[url('/68186657_56.jpg')] bg-cover bg-center"
        ></div>
        <div class="pointer-events-none absolute inset-0 -z-10 bg-black/40 backdrop-blur-sm"></div>

        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white/90 px-6 py-4 shadow-md backdrop-blur-[2px] sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>

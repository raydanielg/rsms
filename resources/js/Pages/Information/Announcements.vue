<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Announcements" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Announcements</h2>
    </template>
    <ComingSoon title="Announcements" subtitle="Coming soon" />
  </AuthenticatedLayout>
</template>

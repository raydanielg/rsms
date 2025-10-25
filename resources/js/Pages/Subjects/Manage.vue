<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ComingSoon from '@/Components/ComingSoon.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Manage Subjects" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Manage Subjects</h2>
    </template>
    <ComingSoon title="Manage Subjects" subtitle="Coming soon" />
  </AuthenticatedLayout>
</template>

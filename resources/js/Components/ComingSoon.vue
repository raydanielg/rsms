<script setup>
const props = defineProps({
  title: { type: String, default: 'Coming Soon' },
  subtitle: { type: String, default: 'This page is under construction.' },
});
</script>

<template>
  <div class="flex min-h-[40vh] flex-col items-center justify-center gap-3 text-center">
    <div class="rounded-full bg-green-50 p-4 text-green-600 shadow">
      <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z"/>
      </svg>
    </div>
    <h3 class="text-2xl font-bold text-gray-900">{{ title }}</h3>
    <p class="max-w-md text-sm text-gray-600">{{ subtitle }}</p>
  </div>
</template>

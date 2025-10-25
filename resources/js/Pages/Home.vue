<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <header class="border-b bg-white">
      <div class="mx-auto max-w-7xl px-4 py-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">{{ appName }}</h1>
        <nav class="space-x-4">
          <Link href="/login" class="text-blue-600 hover:underline">Login</Link>
          <Link href="/register" class="text-blue-600 hover:underline">Register</Link>
        </nav>
      </div>
    </header>
    <main class="mx-auto max-w-7xl px-4 py-12">
      <h2 class="text-2xl font-bold mb-2">Karibu</h2>
      <p class="text-gray-600">Hii ni ukurasa wa mwanzo. Tafadhali ingia au jisajili ili kuendelea.</p>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
const appName = document.title || 'App'
</script>

<style scoped>
</style>

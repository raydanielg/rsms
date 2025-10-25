<template>
  <header class="sticky top-0 z-40 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 border-b">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-8 h-16 grid grid-cols-12 items-center gap-4">
      <!-- Brand (start) -->
      <div class="col-span-8 sm:col-span-6 md:col-span-4 flex items-center gap-3 min-w-0">
        <img src="/logo-trans.png" alt="Logo" class="h-9 w-9 shrink-0" />
        <span class="truncate text-base sm:text-lg md:text-xl font-bold text-green-700" title="RSMS">RSMS</span>
      </div>

      <!-- Nav (center) -->
      <nav aria-label="Primary" class="col-span-4 hidden md:flex items-center justify-center gap-4 lg:gap-6 text-sm font-medium text-gray-700">
        <Link href="/" :class="['px-1.5 py-1 transition', isActive('/') ? 'text-green-700 border-b-2 border-green-600' : 'hover:text-green-700 border-b-2 border-transparent']">Home</Link>
        <Link href="/blog" :class="['px-1.5 py-1 transition', isActive('/blog') ? 'text-green-700 border-b-2 border-green-600' : 'hover:text-green-700 border-b-2 border-transparent']">Blog</Link>
        <Link href="/results/schools" :class="['px-1.5 py-1 transition', isActive('/results/schools') ? 'text-green-700 border-b-2 border-green-600' : 'hover:text-green-700 border-b-2 border-transparent']">Results</Link>
        <Link href="/track" :class="['px-1.5 py-1 transition', isActive('/track') ? 'text-green-700 border-b-2 border-green-600' : 'hover:text-green-700 border-b-2 border-transparent']">Track</Link>
        <Link href="/about" :class="['px-1.5 py-1 transition', isActive('/about') ? 'text-green-700 border-b-2 border-green-600' : 'hover:text-green-700 border-b-2 border-transparent']">About</Link>
        <Link href="/contact" :class="['px-1.5 py-1 transition', isActive('/contact') ? 'text-green-700 border-b-2 border-green-600' : 'hover:text-green-700 border-b-2 border-transparent']">Contact</Link>
      </nav>

      <!-- Actions (end) -->
      <div class="col-span-4 hidden sm:flex items-center justify-end gap-3">
        <template v-if="hasUser">
          <Link :href="route('dashboard')" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">Dashboard</Link>
        </template>
        <template v-else>
          <Link :href="route('login')" class="text-gray-700 hover:text-green-700 font-medium">Login</Link>
          <Link :href="route('register')" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">Start Now</Link>
        </template>
      </div>

      <!-- Mobile menu button (end on small) -->
      <button @click="open = !open" class="col-span-4 md:hidden justify-self-end inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100">
        <svg v-if="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- Mobile drawer -->
    <div v-if="open" class="md:hidden border-t bg-white">
      <div class="mx-auto max-w-7xl px-4 py-3 space-y-3">
        <div class="grid grid-cols-2 gap-3 text-sm">
          <Link href="/" class="py-2 text-gray-700 hover:text-green-700">Home</Link>
          <Link href="/blog" class="py-2 text-gray-700 hover:text-green-700">Blog</Link>
          <Link href="/results/schools" class="py-2 text-gray-700 hover:text-green-700">Results</Link>
          <Link href="/track" class="py-2 text-gray-700 hover:text-green-700">Track</Link>
          <Link href="/about" class="py-2 text-gray-700 hover:text-green-700">About</Link>
          <Link href="/contact" class="py-2 text-gray-700 hover:text-green-700">Contact</Link>
        </div>
        <div class="pt-3 flex items-center gap-3">
          <template v-if="hasUser">
            <Link :href="route('dashboard')" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">Dashboard</Link>
          </template>
          <template v-else>
            <Link :href="route('login')" class="text-gray-700 hover:text-green-700 font-medium">Login</Link>
            <Link :href="route('register')" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">Start Now</Link>
          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
const open = ref(false)
const page = usePage()
const currentPath = computed(() => page.url || '/')
const hasUser = computed(() => !!(page.props && page.props.auth && page.props.auth.user))
function isActive(href) {
  if (href === '/' ) return currentPath.value === '/'
  return currentPath.value === href || currentPath.value.startsWith(href + '/')
}
</script>

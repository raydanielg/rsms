<template>
  <section class="relative bg-gray-900 py-20 sm:py-24 text-white text-center overflow-hidden">
    <div class="absolute inset-0 z-0">
      <img src="/hero-background.jpg" alt="Background" class="h-full w-full object-cover blur-sm" />
      <div class="absolute inset-0 bg-black/50"></div>
    </div>
    <div class="relative z-10 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
      <h2 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
        <span class="block">Mfumo wa Kisasa wa Usimamizi wa Shule</span>
        <span class="block text-green-400">Rahisi. Haraka. Madhubuti.</span>
      </h2>
      <p class="mt-6 mx-auto max-w-2xl text-lg text-gray-200">
        RSMS (Result Slip Management System) hurahisisha usimamizi wa wanafunzi, alama na ripoti ili walimu na wazazi waone maendeleo kwa urahisi.
      </p>
      <div class="mt-8 flex items-center justify-center gap-4">
        <Link :href="route('register')" class="inline-flex items-center rounded-lg bg-green-600 px-6 py-3 text-white font-semibold hover:bg-green-700 transition shadow-md">
          Anza Sasa
        </Link>
        <a href="#video" class="inline-flex items-center rounded-lg bg-white/90 px-6 py-3 text-gray-900 font-semibold hover:bg-white transition shadow-md">
          <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7-11-7z"/></svg>
          Tazama Video
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen bg-gray-100 text-gray-800">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-green-700">{{ appName }}</h1>
        <nav class="space-x-4 flex items-center">
          <Link :href="route('login')" class="text-gray-600 hover:text-green-700 font-medium">Login</Link>
          <Link :href="route('register')" class="px-4 py-2 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition">Start Now</Link>
        </nav>
      </div>
    </header>

    <!-- Hero Section -->
    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
      <h2 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
        <span class="block">Mfumo wa Kisasa wa Usimamizi wa Shule</span>
        <span class="block text-green-600">Rahisi. Haraka. Madhubuti.</span>
      </h2>
      <p class="mt-6 max-w-2xl mx-auto text-lg text-gray-600">
        RSMS (Result Slip Management System) ni suluhisho la kidijitali linalorahisisha usimamizi wa taarifa za wanafunzi, alama, na matokeo, likiwawezesha walimu na wazazi kufuatilia maendeleo kwa urahisi.
      </p>
      <div class="mt-8">
        <Link :href="route('register')" class="inline-block px-8 py-3 bg-green-600 text-white rounded-lg font-bold text-lg hover:bg-green-700 transition shadow-md">
          Anza Sasa (Bure)
        </Link>
      </div>
    </main>

    <!-- Features Section -->
    <section class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-3xl font-bold text-gray-900">Sifa Kuu za Mfumo</h3>
                <p class="mt-4 text-gray-600">Zana zote unazohitaji kwa ajili ya usimamizi bora wa shule yako.</p>
            </div>
            <div class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                <div class="text-center p-6 border border-gray-200 rounded-lg">
                    <h4 class="text-xl font-semibold">Usimamizi wa Wanafunzi</h4>
                    <p class="mt-2 text-gray-500">Weka na fuatilia taarifa zote muhimu za wanafunzi wako sehemu moja.</p>
                </div>
                <div class="text-center p-6 border border-gray-200 rounded-lg">
                    <h4 class="text-xl font-semibold">Uchakataji wa Matokeo</h4>
                    <p class="mt-2 text-gray-500">Ingiza alama na toa ripoti za matokeo (result slips) kwa haraka na bila makosa.</p>
                </div>
                <div class="text-center p-6 border border-gray-200 rounded-lg">
                    <h4 class="text-xl font-semibold">Ripoti za Kitaaluma</h4>
                    <p class="mt-2 text-gray-500">Pata takwimu na ripoti za kina kuhusu maendeleo ya wanafunzi na shule.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Coming Soon Section -->
    <section class="py-20">
        <div class="text-center">
            <h3 class="text-3xl font-bold text-gray-900">Vipengele Vipya Vinakuja Hivi Karibuni</h3>
            <p class="mt-4 text-gray-600">Tunaendelea kuboresha mfumo. Tarajia usimamizi wa ratiba, mawasiliano ya wazazi, na mengine mengi!</p>
            <div class="mt-6">
                <span class="inline-block bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-3 py-1 rounded-full">Coming Soon</span>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="text-center">
            <p>&copy; {{ new Date().getFullYear() }} {{ appName }}. Haki zote zimehifadhiwa.</p>
        </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
const appName = document.title.split(' - ')[1] || document.title || 'RSMS';
</script>

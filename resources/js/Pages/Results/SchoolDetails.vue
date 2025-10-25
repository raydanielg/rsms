<template>
  <div class="min-h-screen bg-white antialiased">
    <Header />

    <section class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-8 py-10">
      <!-- Breadcrumbs -->
      <nav class="mb-6 text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="flex items-center gap-2">
          <li><Link href="/" class="hover:text-green-700">Home</Link></li>
          <li>/</li>
          <li><Link href="/results/schools" class="hover:text-green-700">Results</Link></li>
          <li>/</li>
          <li class="text-gray-900 font-medium truncate max-w-[12rem] sm:max-w-none">Details</li>
        </ol>
      </nav>

      <!-- Header -->
      <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ school?.school_name || 'School' }}</h1>
          <p class="mt-1 text-sm text-gray-600">Center No: {{ id }}</p>
        </div>
        <div class="flex items-center gap-2">
          <Link :href="route('results.schools.show', { id })" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">View Exams</Link>
          <Link href="/results/schools" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">Back to Schools</Link>
        </div>
      </div>

      <!-- Summary cards -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Teachers</div>
          <div class="mt-1 text-2xl font-bold text-gray-900">{{ (counts?.teachers ?? 0).toLocaleString() }}</div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Classes</div>
          <div class="mt-1 text-2xl font-bold text-gray-900">{{ (counts?.classes ?? 0).toLocaleString() }}</div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Students</div>
          <div class="mt-1 text-2xl font-bold text-gray-900">{{ (counts?.students ?? 0).toLocaleString() }}</div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Subjects</div>
          <div class="mt-1 text-2xl font-bold text-gray-900">{{ (counts?.subjects ?? 0).toLocaleString() }}</div>
        </div>
      </div>

      <!-- Registrant / School info -->
      <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
          <div class="mb-3 text-sm font-semibold text-gray-800">School</div>
          <div class="grid grid-cols-3 gap-y-2 text-sm">
            <div class="text-gray-500">Name</div>
            <div class="col-span-2 text-gray-900">{{ school?.school_name || '-' }}</div>
            <div class="text-gray-500">Center No</div>
            <div class="col-span-2 text-gray-900">{{ school?.school_number || id }}</div>
            <div class="text-gray-500">Region</div>
            <div class="col-span-2 text-gray-900">{{ school?.region || '-' }}</div>
          </div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
          <div class="mb-3 text-sm font-semibold text-gray-800">Registrant</div>
          <div class="grid grid-cols-3 gap-y-2 text-sm">
            <div class="text-gray-500">Name</div>
            <div class="col-span-2 text-gray-900">{{ school?.name || '-' }}</div>
            <div class="text-gray-500">Username</div>
            <div class="col-span-2 text-gray-900">{{ school?.username || '-' }}</div>
            <div class="text-gray-500">Email</div>
            <div class="col-span-2 text-gray-900">{{ school?.email || '-' }}</div>
            <div class="text-gray-500">Phone</div>
            <div class="col-span-2 text-gray-900">{{ school?.phone || '-' }}</div>
          </div>
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  id: { type: [String, Number], required: true },
  school: { type: Object, default: null },
  counts: { type: Object, default: () => ({}) },
})

const id = props.id
const school = props.school
const counts = props.counts
</script>

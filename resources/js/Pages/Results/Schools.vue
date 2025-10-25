<template>
  <div class="min-h-screen bg-white antialiased">
    <Header />

    <section class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-8 py-10">
      <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Schools</h1>
          <div class="w-full sm:w-80 relative">
            <input v-model.trim="query" type="text" placeholder="Search schools..." class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-green-600" />
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27a6.471 6.471 0 0 0 1.57-4.23C16 6.01 13.31 3.32 10 3.32S4 6.01 4 9.5 6.69 15.68 10 15.68c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 5 1.49-1.49-5-5zM10 13.68A4.18 4.18 0 0 1 5.82 9.5 4.18 4.18 0 0 1 10 5.32 4.18 4.18 0 0 1 14.18 9.5 4.18 4.18 0 0 1 10 13.68z"/></svg>
          </div>
        </div>

        <div class="flex flex-wrap gap-2 text-sm">
          <button type="button" @click="activeLetter = ''" :class="['rounded-md px-2.5 py-1', activeLetter==='' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">All</button>
          <button v-for="l in letters" :key="l" type="button" @click="activeLetter = l" :class="['rounded-md px-2.5 py-1', activeLetter===l ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">{{ l }}</button>
        </div>
      </div>

      <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-gray-700">Center No</th>
              <th class="px-4 py-2 text-left font-semibold text-gray-700">School Name</th>
              <th class="px-4 py-2 text-right font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="school in filtered" :key="school.id" class="hover:bg-gray-50">
              <td class="px-4 py-2 text-gray-900 whitespace-nowrap">{{ school.id }}</td>
              <td class="px-4 py-2 text-gray-800">
                <span class="truncate inline-block max-w-[32rem]" :title="school.name">{{ school.name }}</span>
              </td>
              <td class="px-4 py-2">
                <div class="flex items-center justify-end gap-2">
                  <Link :href="`/results/schools/${school.id}`" class="inline-flex items-center justify-center h-8 w-8 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100" aria-label="View">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9z"/></svg>
                    <span class="sr-only">View</span>
                  </Link>
                  <Link :href="`/results/schools/${school.id}/details`" class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-green-600 text-white hover:bg-green-700" aria-label="Details">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11 9h2v2h-2V9zm0 4h2v6h-2v-6z"/><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
                    <span class="sr-only">Details</span>
                  </Link>
                </div>
              </td>
            </tr>
            <tr v-if="!loading && filtered.length === 0">
              <td colspan="3" class="px-4 py-6 text-center text-gray-600">No schools found.</td>
            </tr>
            <tr v-if="loading">
              <td colspan="3" class="px-4 py-6 text-center text-gray-600">Loading...</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="filtered.length === 0" class="mt-12 text-center text-gray-600">No schools found.</div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { Link } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

const query = ref('')
const activeLetter = ref('')
const letters = Array.from('ABCDEFGHIJKLMNOPQRSTUVWXYZ')

const schools = ref([])
const loading = ref(false)

async function loadSchools() {
  loading.value = true
  try {
    const res = await fetch('/api/public/schools', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await res.json()
    const mapped = Array.isArray(data) ? data.map(r => ({ id: r.school_number, name: r.school_name || String(r.school_number) })) : []
    mapped.sort((a,b) => a.name.localeCompare(b.name))
    schools.value = mapped
  } catch (e) {
    schools.value = []
  } finally { loading.value = false }
}

onMounted(() => { loadSchools() })

const filtered = computed(() => {
  const q = (query.value || '').toLowerCase()
  let list = schools.value
  if (activeLetter.value) list = list.filter(s => (s.name || '').toUpperCase().startsWith(activeLetter.value))
  if (q) list = list.filter(s => (s.name || '').toLowerCase().includes(q))
  return list
})

// no-op helper removed: using a single table now
</script>

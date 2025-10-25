<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const page = usePage()
const items = ref([])

onMounted(() => {
  if (page.props && Array.isArray(page.props.items)) items.value = page.props.items
})

const q = ref('')
const filtered = computed(() => {
  const term = q.value.trim().toLowerCase()
  if (!term) return items.value
  return items.value.filter(t => (t.name || '').toLowerCase().includes(term))
})

const previewItem = ref(null)
const showPreview = ref(false)
function preview(t) { previewItem.value = t; showPreview.value = true }

function printableHtml(t) {
  const now = new Date().toLocaleString()
  return `<!doctype html>
  <html><head><meta charset="utf-8" />
  <title>${t.name} — Class Timetable</title>
  <style>body{font-family:Arial,system-ui,sans-serif;margin:24px;color:#0f172a}h1{font-size:20px;margin:0 0 8px}.meta{font-size:12px;color:#475569;margin-bottom:16px}table{width:100%;border-collapse:collapse;font-size:12px}th,td{border:1px solid #cbd5e1;padding:8px}th{background:#f1f5f9;text-align:left}</style>
  </head><body>
  <h1>${t.name}</h1>
  <div class="meta">Generated: ${now}</div>
  <table><thead><tr><th>Day</th><th>Time</th><th>Class</th><th>Subject</th><th>Teacher</th></tr></thead>
  <tbody>
    <tr><td>Monday</td><td>08:00 - 09:00</td><td>Form I A</td><td>Mathematics</td><td>Mr. John</td></tr>
    <tr><td>Monday</td><td>09:00 - 10:00</td><td>Form I A</td><td>English</td><td>Ms. Grace</td></tr>
  </tbody></table>
  </body></html>`
}

function download(t) {
  const w = window.open('', '_blank', 'noopener,noreferrer')
  if (!w) return
  w.document.open(); w.document.write(printableHtml(t)); w.document.close();
  setTimeout(() => { try { w.focus(); w.print(); } catch(e) {} }, 250)
}
</script>

<template>
  <Head title="Class Timetables" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Class Timetables</h2>
        <Link :href="route('timetables.create', { type: 'class' })" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
          <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Create
        </Link>
      </div>
    </template>

    <div class="space-y-4">
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div class="text-sm font-medium text-slate-700">Latest Class Timetables</div>
          <div class="flex items-center gap-2">
            <div class="relative">
              <input v-model.trim="q" type="text" placeholder="Search..." class="w-full sm:w-64 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              <svg class="pointer-events-none absolute right-2 top-2.5 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
            </div>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-700">
              <tr>
                <th class="px-3 py-2 text-left font-semibold">#</th>
                <th class="px-3 py-2 text-left font-semibold">Timetable Name</th>
                <th class="px-3 py-2 text-left font-semibold">Category</th>
                <th class="px-3 py-2 text-left font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="(t, i) in filtered" :key="t.id" class="hover:bg-slate-50">
                <td class="px-3 py-2 text-slate-800">{{ i + 1 }}</td>
                <td class="px-3 py-2 text-slate-800">{{ t.name }}</td>
                <td class="px-3 py-2 text-slate-700">
                  <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Class</span>
                </td>
                <td class="px-3 py-2">
                  <div class="flex items-center gap-2">
                    <button @click="preview(t)" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                      <svg class="mr-1.5 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                      View
                    </button>
                    <button @click="download(t)" class="inline-flex items-center rounded-md bg-sky-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-sky-700">
                      <svg class="mr-1.5 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M4 21h16"/></svg>
                      Download
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!filtered.length">
                <td colspan="4" class="px-3 py-6 text-center text-slate-500">No class timetables yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Preview Modal -->
  <div v-if="showPreview" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-4xl overflow-hidden rounded-xl bg-white shadow-xl">
      <div class="flex items-center justify-between border-b px-4 py-3">
        <div class="text-sm font-semibold text-slate-800">Preview: {{ previewItem && previewItem.name }}</div>
        <div class="flex items-center gap-2">
          <button @click="download(previewItem)" class="inline-flex items-center rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700">
            <svg class="mr-1.5 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M4 21h16"/></svg>
            Download (PDF)
          </button>
          <button @click="showPreview=false" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Close</button>
        </div>
      </div>
      <div class="max-h-[70vh] overflow-auto p-4">
        <div class="text-lg font-semibold text-slate-800">{{ previewItem && previewItem.name }}</div>
        <div class="mb-3 text-xs text-slate-500">Preview example layout</div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">Day</th>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">Time</th>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">Class</th>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">Subject</th>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">Teacher</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr>
                <td class="px-3 py-2">Monday</td>
                <td class="px-3 py-2">08:00 - 09:00</td>
                <td class="px-3 py-2">Form I A</td>
                <td class="px-3 py-2">Mathematics</td>
                <td class="px-3 py-2">Mr. John</td>
              </tr>
              <tr>
                <td class="px-3 py-2">Monday</td>
                <td class="px-3 py-2">09:00 - 10:00</td>
                <td class="px-3 py-2">Form I A</td>
                <td class="px-3 py-2">English</td>
                <td class="px-3 py-2">Ms. Grace</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<template>
  <div class="min-h-screen bg-white antialiased">
    <Header />

    <section class="mx-auto max-w-5xl px-3 sm:px-4 lg:px-8 py-10">
      <div class="mb-6">
        <div class="text-center space-y-1">
          <div class="text-[11px] sm:text-xs tracking-wide text-black uppercase">Student Tracker</div>
          <div class="text-xl sm:text-2xl font-extrabold text-emerald-800 uppercase">Search Student</div>
        </div>
      </div>

      <div class="mx-auto max-w-3xl">
        <div class="relative">
          <input
            v-model.trim="query"
            @input="onInput"
            type="text"
            placeholder="Type student name or reg no..."
            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600"
          />
          <div v-if="loading" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2" opacity="0.25"/><path d="M4 12a8 8 0 0 1 8-8" stroke-width="2"/></svg>
          </div>
        </div>

        <div v-if="showDropdown" class="relative mt-2">
          <div class="absolute z-10 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
            <ul class="divide-y divide-gray-100 text-sm">
              <li v-for="s in suggestions" :key="s.id" class="flex items-center justify-between px-4 py-2 hover:bg-gray-50">
                <button type="button" class="w-full text-left" @click="selectStudent(s)">
                  <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                      <div class="truncate font-medium text-gray-900">{{ s.name }}</div>
                      <div class="truncate text-xs text-gray-600">{{ s.reg_no }} • {{ s.class_name }} • {{ s.school_name }}</div>
                    </div>
                    <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700">{{ s.sex }}</span>
                  </div>
                </button>
              </li>
              <li v-if="!loading && suggestions.length === 0" class="px-4 py-4 text-center text-gray-600">No results</li>
            </ul>
          </div>
        </div>
      </div>

      <div v-if="selected" class="mt-8">
        <div class="mx-auto max-w-3xl rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-lg font-semibold text-gray-900">{{ selected.name }}</div>
              <div class="mt-1 text-sm text-gray-700">Reg No: <span class="font-medium">{{ selected.reg_no }}</span></div>
              <div class="mt-1 text-sm text-gray-700">Class: <span class="font-medium">{{ selected.class_name }}</span></div>
              <div class="mt-1 text-sm text-gray-700">School: <span class="font-medium">{{ selected.school_name }} ({{ selected.school_number }})</span></div>
              <div class="mt-1 text-sm text-gray-700">Sex: <span class="font-medium">{{ selected.sex }}</span></div>
            </div>
            <div class="shrink-0 flex gap-2">
              <Link :href="`/results/schools/${selected.school_number}`" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">View School</Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Results by Exam Type -->
      <div v-if="selected" class="mx-auto mt-8 max-w-5xl">
        <div class="mb-3 text-center text-sm font-semibold text-slate-800">Student Results</div>
        <div class="grid gap-4 lg:grid-cols-3">
          <div v-for="(items, type) in groupedByType" :key="type" class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-800 uppercase">{{ type }}</div>
            <ul class="divide-y divide-slate-100 text-sm">
              <li v-for="ex in items" :key="ex.id" class="p-3">
                <div class="flex items-start justify-between">
                  <div>
                    <div class="font-medium text-slate-900">{{ ex.name }}</div>
                    <div class="text-xs text-slate-600">Year {{ ex.year }} • Term {{ ex.term }} • DIV {{ ex.division }} • AVG {{ ex.aggregate }}</div>
                  </div>
                  <button type="button" @click="expandExam(ex.id)" :disabled="expanded.has(ex.id)"
                          class="rounded-md px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-50 disabled:cursor-default disabled:opacity-60">
                    {{ expanded.has(ex.id) ? 'Expanded' : 'Expand' }}
                  </button>
                </div>
                <div v-if="expanded.has(ex.id)" class="mt-3 overflow-x-auto rounded-lg border border-slate-200">
                  <table class="min-w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50">
                      <tr>
                        <th class="px-2 py-1 text-left font-semibold text-slate-800">SUBJECT</th>
                        <th class="px-2 py-1 text-right font-semibold text-slate-800">MARKS</th>
                        <th class="px-2 py-1 text-right font-semibold text-slate-800">GRADE</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                      <tr v-for="sub in ex.subjects" :key="sub.code" class="hover:bg-slate-50">
                        <td class="px-2 py-1 text-slate-900">
                          <div class="leading-tight">
                            <div>{{ sub.code }}</div>
                            <div v-if="sub.name" class="text-[10px] text-slate-500">{{ sub.name }}</div>
                          </div>
                        </td>
                        <td class="px-2 py-1 text-right text-slate-800">{{ sub.marks }}</td>
                        <td class="px-2 py-1 text-right">
                          <span :class="gradePill(sub.grade)">{{ sub.grade }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </li>
            </ul>
          </div>

          <!-- Behaviours panel -->
          <div class="rounded-xl border border-amber-200 bg-amber-50/60 shadow-sm lg:col-span-3">
            <div class="border-b bg-amber-100/70 px-4 py-2 text-sm font-semibold text-amber-900 uppercase">Behaviours</div>
            <div class="p-3">
              <ul class="divide-y divide-amber-100 text-sm">
                <li v-for="b in behaviours" :key="b.id" class="py-2">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <div class="font-medium text-amber-900">{{ b.type }}</div>
                      <div class="text-amber-800/80">{{ b.note }}</div>
                    </div>
                    <div class="shrink-0 text-xs text-amber-700">{{ formatDate(b.created_at) }}</div>
                  </div>
                </li>
                <li v-if="!behavioursLoading && behaviours.length === 0" class="py-4 text-center text-amber-800/80">No behaviours found.</li>
                <li v-if="behavioursLoading" class="py-4 text-center text-amber-800/80">Loading behaviours...</li>
              </ul>
            </div>
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
import { ref, computed } from 'vue'

const query = ref('')
const loading = ref(false)
const showDropdown = ref(false)
const suggestions = ref([])
const selected = ref(null)
let debounceTimer = null

// Results state
const results = ref([]) // list of exams with subjects
const expanded = ref(new Set())
const resultsLoading = ref(false)

// Behaviours state
const behaviours = ref([])
const behavioursLoading = ref(false)

const groupedByType = computed(() => {
  const map = {}
  for (const ex of results.value) {
    const key = (ex.type || 'Exam').toString().toUpperCase()
    if (!map[key]) map[key] = []
    map[key].push(ex)
  }
  // keep each group's exams newest first by year/term/id if present
  for (const k of Object.keys(map)) {
    map[k].sort((a,b) => (b.year - a.year) || (b.term - a.term) || (b.id - a.id))
  }
  return map
})

async function search(q) {
  if (!q) { suggestions.value = []; return }
  loading.value = true
  try {
    const res = await fetch(`/api/public/students/search?query=${encodeURIComponent(q)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await res.json()
    suggestions.value = Array.isArray(data) ? data : []
  } catch (e) { suggestions.value = [] }
  finally { loading.value = false }
}

function onInput() {
  showDropdown.value = true
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => search(query.value), 300)
}

async function selectStudent(s) {
  showDropdown.value = false
  query.value = s.name
  try {
    const res = await fetch(`/api/public/students/${s.id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await res.json()
    selected.value = data && data.id ? data : null
  } catch (e) { selected.value = s }

  // fetch results and behaviours in parallel
  expanded.value = new Set()
  resultsLoading.value = true
  behavioursLoading.value = true
  try {
    const [r1, r2] = await Promise.all([
      fetch(`/api/public/students/${s.id}/results`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
      fetch(`/api/public/students/${s.id}/behaviours`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
    ])
    const data1 = await r1.json()
    const data2 = await r2.json()
    results.value = Array.isArray(data1?.exams) ? data1.exams : []
    behaviours.value = Array.isArray(data2) ? data2 : []
  } finally {
    resultsLoading.value = false
    behavioursLoading.value = false
  }
}

function expandExam(id) {
  // expand only; do not collapse once expanded
  const set = new Set(expanded.value)
  set.add(id)
  expanded.value = set
}

function gradePill(grade) {
  const g = (grade || '').toUpperCase()
  if (g === 'A') return 'inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-800'
  if (g === 'B') return 'inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-[11px] font-semibold text-green-800'
  if (g === 'C') return 'inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800'
  if (g === 'D') return 'inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-[11px] font-semibold text-orange-800'
  return 'inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-semibold text-rose-800'
}

function formatDate(dt) {
  try { return new Date(dt).toLocaleDateString() } catch { return dt }
}
</script>

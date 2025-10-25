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
          <li class="text-gray-900 font-medium truncate max-w-[12rem] sm:max-w-none">{{ school?.name || 'School' }}</li>
        </ol>
      </nav>

      <!-- Header -->
      <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ school?.name || 'School' }}</h1>
          <p v-if="school" class="mt-1 text-sm text-gray-600">{{ school.location }} • Students {{ school.students.toLocaleString() }}</p>
        </div>
      </div>

      <!-- Selectors -->
      <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 sm:grid-cols-3">
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Year</label>
            <select v-model="selectedYear" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option :value="''">Select year</option>
              <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Exam</label>
            <select v-model="selectedExamId" :disabled="!selectedYear" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600 disabled:bg-gray-50 disabled:text-gray-400">
              <option :value="''">Select exam</option>
              <option v-for="ex in filteredExams" :key="ex.id" :value="ex.id">{{ ex.name }} • Term {{ ex.term }}</option>
            </select>
          </div>
          <div class="flex items-end">
            <Link :href="selectedExamId ? route('results.schools.exam', { id: props.id, examId: selectedExamId }) : '#'"
                  :class="['inline-flex items-center rounded-md px-4 py-2 text-sm font-semibold shadow-sm transition', selectedExamId ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-200 text-gray-500 cursor-not-allowed']">
              View Results
            </Link>
          </div>
        </div>
      </div>

      <!-- Exams list (optional helper) -->
      <div class="mt-6">
        <h3 class="mb-3 text-sm font-semibold text-gray-700">Available exams</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <button type="button" v-for="(ex, idx) in filteredExams" :key="ex.id" :class="['text-left rounded-xl border p-4 shadow-sm transition hover:shadow', cardBg(idx)]" @click="openExamModal(ex)">
            <div class="flex items-start justify-between">
              <div>
                <div class="text-base font-semibold text-gray-900">{{ ex.name }}</div>
                <div class="mt-0.5 text-xs text-gray-700">Year {{ ex.year }} • Term {{ ex.term }}</div>
                <span :class="['mt-2 inline-block rounded-full px-2 py-0.5 text-xs font-medium', typeBadgeClass(ex.type)]">{{ ex.type }}</span>
              </div>
              <span class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-white/80 text-gray-700 ring-1 ring-inset ring-gray-300">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9z"/></svg>
              </span>
            </div>
          </button>
        </div>

        <!-- Exam Classes Modal -->
        <div v-if="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
          <div class="w-full max-w-lg rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b px-4 py-3">
              <div class="text-base font-semibold text-gray-900">{{ (modalExam && modalExam.name) || 'Exam' }}</div>
              <button @click="closeModal" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <div class="p-4">
              <div class="mb-3 text-sm text-gray-600">Select a class to view results.</div>
              <div class="overflow-hidden rounded-lg border">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-3 py-2 text-left font-semibold text-gray-700">Class</th>
                      <th class="px-3 py-2 text-right font-semibold text-gray-700">Action</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <tr v-for="cls in modalClasses" :key="cls">
                      <td class="px-3 py-2 text-gray-900">{{ cls }}</td>
                      <td class="px-3 py-2 text-right">
                        <Link :href="route('results.schools.exam', { id: props.id, examId: (modalExam && modalExam.id) }) + `?class_name=${encodeURIComponent(cls)}`"
                              class="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700">
                          View
                        </Link>
                      </td>
                    </tr>
                    <tr v-if="!loadingClasses && modalClasses.length === 0">
                      <td colspan="2" class="px-3 py-4 text-center text-gray-600">No classes assigned for this exam.</td>
                    </tr>
                    <tr v-if="loadingClasses">
                      <td colspan="2" class="px-3 py-4 text-center text-gray-600">Loading classes...</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="flex justify-end gap-2 border-t px-4 py-3">
              <button @click="closeModal" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Close</button>
            </div>
          </div>
        </div>
        <div v-if="!loading && filteredExams.length === 0" class="text-sm text-gray-500">No exams for the selected year.</div>
        <div v-if="loading" class="text-sm text-gray-500">Loading exams...</div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { Link } from '@inertiajs/vue3'
import { computed, ref, onMounted } from 'vue'

const props = defineProps({ id: { type: [String, Number], required: true } })

// Mock data for demo; replace with API data later
const allSchools = [
  { id: 1, name: 'Green Valley Secondary School', location: 'Dodoma', students: 1240 },
  { id: 2, name: 'Mlimani High School', location: 'Dar es Salaam', students: 980 },
  { id: 3, name: 'Kilimanjaro Girls School', location: 'Kilimanjaro', students: 820 },
  { id: 4, name: 'Lakeview Secondary', location: 'Mwanza', students: 1105 },
  { id: 5, name: 'Coastal Academy', location: 'Tanga', students: 640 },
  { id: 6, name: 'Savanna Boys High', location: 'Arusha', students: 770 },
]

const school = computed(() => {
  const pid = String(props.id)
  return allSchools.find(s => String(s.id) === pid)
})

// Exams fetched from API for this school
const allExams = ref([])
const loading = ref(false)
async function loadExams() {
  loading.value = true
  try {
    const res = await fetch(`/api/public/schools/${props.id}/exams`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await res.json()
    allExams.value = Array.isArray(data) ? data : []
  } catch (e) { allExams.value = [] } finally { loading.value = false }
}

// Modal state for exam classes
const modalOpen = ref(false)
const modalExam = ref(null)
const modalClasses = ref([])
const loadingClasses = ref(false)

function closeModal() {
  modalOpen.value = false
  modalExam.value = null
  modalClasses.value = []
}

async function openExamModal(exam) {
  modalExam.value = exam
  modalOpen.value = true
  loadingClasses.value = true
  try {
    const res = await fetch(`/api/public/schools/${props.id}/exams/${exam.id}/classes`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await res.json()
    modalClasses.value = Array.isArray(data) ? data : []
  } catch (e) {
    modalClasses.value = []
  } finally {
    loadingClasses.value = false
  }
}
onMounted(() => { loadExams() })

const selectedYear = ref('')
const selectedExamId = ref('')

const years = computed(() => Array.from(new Set((allExams.value || []).map(e => e.year))).sort((a,b)=>b-a))
const filteredExams = computed(() => selectedYear.value ? (allExams.value || []).filter(e => e.year === Number(selectedYear.value)) : (allExams.value || []))

function typeBadgeClass(type) {
  const t = String(type || '').toLowerCase()
  if (t === 'midterm') return 'bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-200'
  if (t === 'terminal') return 'bg-purple-100 text-purple-800 ring-1 ring-inset ring-purple-200'
  if (t === 'mock') return 'bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-200'
  if (t === 'trial') return 'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-200'
  return 'bg-green-100 text-green-800 ring-1 ring-inset ring-green-200'
}
function cardBg(idx) {
  const variants = [
    'border-blue-200 bg-gradient-to-br from-blue-50 to-white',
    'border-green-200 bg-gradient-to-br from-green-50 to-white',
    'border-amber-200 bg-gradient-to-br from-amber-50 to-white',
    'border-purple-200 bg-gradient-to-br from-purple-50 to-white',
    'border-rose-200 bg-gradient-to-br from-rose-50 to-white',
  ]
  return variants[idx % variants.length]
}
</script>

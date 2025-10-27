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
          <li><Link :href="route('results.schools.show', { id: schoolId })" class="hover:text-green-700">{{ schoolTitle || 'School' }}</Link></li>
          <li>/</li>
          <li class="text-gray-900 font-medium truncate max-w-[12rem] sm:max-w-none">{{ (meta && meta.name) || 'Exam' }}</li>
        </ol>
      </nav>

      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <div class="text-center space-y-1">
              <div class="text-[11px] sm:text-xs tracking-wide text-gray-600 uppercase">Regional Administration and Local Government</div>
              <div class="text-[11px] sm:text-xs tracking-wide text-gray-600 uppercase">Form Results</div>
              <div class="text-xl sm:text-2xl font-extrabold text-emerald-800 uppercase" v-if="meta">
                {{ ((meta && meta.name) || 'Exam') }} Examination Results
                <span v-if="meta && meta.year">, {{ meta.year }}</span>
                <span v-if="selectedClass" class="text-emerald-900"> - {{ String(selectedClass).toUpperCase() }}</span>
              </div>
              <div class="text-sm sm:text-base font-semibold text-sky-700 uppercase">{{ String(schoolId) }} - {{ (schoolTitle || 'School Centre') }} </div>
            </div>
          </div>
          <div class="shrink-0 flex items-center gap-2 print:hidden">
            <select v-model="selectedClass" @change="loadResults" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option value="">Select Class</option>
              <option v-for="c in classes" :key="c" :value="c">{{ c }}</option>
            </select>
            <button @click="doPrint" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
              <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 8H5a3 3 0 0 0-3 3v5h4v4h12v-4h4v-5a3 3 0 0 0-3-3zM7 18h10v2H7v-2zm13-3H4v-4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v4z"/><path d="M17 2H7v4h10V2z"/></svg>
              Print
            </button>
          </div>
        </div>
      </div>

      <!-- Division Performance Summary (centered, compact) -->
      <div class="mb-6 text-center">
        <div class="text-center text-xs font-semibold text-emerald-800 uppercase">Division Performance Summary</div>
        <div class="mt-2 overflow-x-auto">
          <div class="mx-auto inline-block rounded-lg border border-emerald-200 bg-emerald-50/70 p-1.5 shadow-sm ring-1 ring-inset ring-emerald-100">
            <table class="divide-y divide-emerald-200 text-xs">
              <thead>
                <tr class="bg-emerald-100/70">
                  <th class="px-3 py-1 text-left font-semibold text-emerald-900">SEX</th>
                  <th class="px-3 py-1 text-center font-semibold text-emerald-900">I</th>
                  <th class="px-3 py-1 text-center font-semibold text-emerald-900">II</th>
                  <th class="px-3 py-1 text-center font-semibold text-emerald-900">III</th>
                  <th class="px-3 py-1 text-center font-semibold text-emerald-900">IV</th>
                  <th class="px-3 py-1 text-center font-semibold text-emerald-900">0</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-emerald-100">
                <tr v-for="row in summaryRows" :key="row.label" class="bg-white/70">
                  <td class="px-3 py-1 font-medium text-emerald-900">{{ row.label }}</td>
                  <td class="px-3 py-1 text-center text-emerald-900">{{ row.I }}</td>
                  <td class="px-3 py-1 text-center text-emerald-900">{{ row.II }}</td>
                  <td class="px-3 py-1 text-center text-emerald-900">{{ row.III }}</td>
                  <td class="px-3 py-1 text-center text-emerald-900">{{ row.IV }}</td>
                  <td class="px-3 py-1 text-center text-emerald-900">{{ row['0'] }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      
      <!-- Readonly Marks Grid -->
      <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-lg">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-1 text-left font-semibold text-rose-900">CNO</th>
              <th class="px-3 py-2 text-left font-semibold text-slate-800">STUDENT NAME</th>
              <th class="px-3 py-2 text-left font-semibold text-slate-800">SEX</th>
              <th v-for="col in subjectColumns" :key="col.key" class="px-3 py-2 text-center font-semibold text-slate-800">
                <div class="leading-tight">
                  <div>{{ col.displayName }}</div>
                </div>
              </th>
              <th class="px-3 py-2 text-center font-semibold text-slate-800">TOTAL</th>
              <th class="px-3 py-2 text-center font-semibold text-slate-800">AVG</th>
              <th class="px-3 py-2 text-center font-semibold text-slate-800">GRADE</th>
              <th class="px-3 py-2 text-center font-semibold text-slate-800">DIV</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="s in students" :key="s.reg_no" class="hover:bg-slate-50">
              <td class="px-3 py-2 text-slate-900 whitespace-nowrap">{{ s.reg_no }}</td>
              <td class="px-3 py-2 text-slate-900">{{ s.name || s.student_name || '—' }}</td>
              <td class="px-3 py-2 text-slate-800">{{ s.sex }}</td>
              <td v-for="col in subjectColumns" :key="col.key" class="px-3 py-2 text-center text-slate-800">{{ markFor(s, col) }}</td>
              <td class="px-3 py-2 text-center text-slate-800">{{ totalFor(s) }}</td>
              <td class="px-3 py-2 text-center text-slate-800">{{ avgFor(s) }}</td>
              <td class="px-3 py-2 text-center text-slate-800">{{ gradeFor(s) }}</td>
              <td class="px-3 py-2 text-center text-slate-800">{{ s.division }}</td>
            </tr>
            <tr v-if="!loading && students.length === 0">
              <td :colspan="3 + subjectColumns.length + 4" class="px-3 py-8 text-center text-gray-600">No results found.</td>
            </tr>
            <tr v-if="loading">
              <td :colspan="3 + subjectColumns.length + 4" class="px-3 py-8 text-center text-gray-600">Loading results...</td>
            </tr>
          </tbody>
        </table>
      </div>

      

      <!-- Performance by Subject -->
      <div class="mt-10">
        <div class="mb-3 text-center text-sm font-semibold text-slate-800">Performance by Subject</div>
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-lg">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-slate-100">
              <tr>
                <th class="px-3 py-2 text-left font-semibold text-slate-800">#</th>
                <th class="px-3 py-2 text-left font-semibold text-slate-800">Subject</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-800">Candidates</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-800">Pass %</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-800">Fail %</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-800">A</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-800">B</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-800">C</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-800">D</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-800">F</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-800">Average</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="row in subjectStats" :key="row.key" class="hover:bg-slate-50">
                <td class="px-3 py-2 text-sm font-semibold text-slate-700">{{ row.position }}</td>
                <td class="px-3 py-2 text-slate-900">
                  <div class="flex items-center gap-2">
                    <span class="font-medium">{{ row.displayName }}</span>
                    <span v-if="row.position === 1" class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">Best</span>
                    <span v-else-if="row.position === subjectStats.length" class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700">Lowest</span>
                  </div>
                </td>
                <td class="px-3 py-2 text-center text-slate-700">{{ row.candidateCount }}</td>
                <td class="px-3 py-2 text-center">
                  <span :class="passRateClass(row.passRate)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">
                    {{ row.passRate }}%
                  </span>
                </td>
                <td class="px-3 py-2 text-center">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-rose-100 text-rose-700">
                    {{ row.failRate }}%
                  </span>
                </td>
                <td class="px-3 py-2 text-center text-emerald-700 font-medium">{{ row.A }}</td>
                <td class="px-3 py-2 text-center text-emerald-600">{{ row.B }}</td>
                <td class="px-3 py-2 text-center text-amber-600">{{ row.C }}</td>
                <td class="px-3 py-2 text-center text-orange-500">{{ row.D }}</td>
                <td class="px-3 py-2 text-center text-rose-600 font-semibold">{{ row.F }}</td>
                <td class="px-3 py-2 text-center text-slate-900 font-semibold">{{ row.avg }}</td>
              </tr>
              <tr v-if="subjectStats.length === 0">
                <td colspan="10" class="px-3 py-8 text-center text-gray-600">No subject data.</td>
              </tr>
            </tbody>
          </table>
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
import { ref, computed, onMounted } from 'vue'

const props = defineProps({ schoolId: { type: [String, Number], required: true }, examId: { type: Number, required: true } })

const loading = ref(false)
const schoolTitle = ref('')
const meta = ref(null) // { id,name,type,year,term }
const summary = ref({ F: { I:0, II:0, III:0, IV:0, 0:0 }, M: { I:0, II:0, III:0, IV:0, 0:0 }, T: { I:0, II:0, III:0, IV:0, 0:0 } })
const students = ref([])
const classes = ref([])
const selectedClass = ref('')

const summaryRows = computed(() => [
  { label: 'F', ...summary.value.F },
  { label: 'M', ...summary.value.M },
  { label: 'T', ...summary.value.T },
])

const bestThree = computed(() => {
  const list = Array.isArray(students.value) ? students.value.slice() : []
  list.sort((a,b) => (b.aggregate||0) - (a.aggregate||0))
  return list.slice(0,3)
})

const failedThree = computed(() => {
  const list = (Array.isArray(students.value) ? students.value : []).filter(s => String(s.division||'').toUpperCase() === '0')
  list.sort((a,b) => (a.aggregate||0) - (b.aggregate||0))
  return list.slice(0,3)
})

function formatSubjects(list) {
  if (!Array.isArray(list)) return ''
  return list.map(it => `${it.code} - ${it.grade}`).join('  •  ')
}

function chooseSubjectDisplayName(subject, fallbackIndex) {
  const candidates = [subject?.name, subject?.title, subject?.label, subject?.full_name, subject?.code]
  const withLetters = candidates.find((c) => typeof c === 'string' && /[A-Za-z]/.test(c))
  if (withLetters) return withLetters
  const anyValue = candidates.find((c) => typeof c === 'string' && c.trim() !== '')
  if (anyValue) return anyValue
  return `Subject ${fallbackIndex}`
}

const subjectColumns = computed(() => {
  const map = new Map()
  let autoIndex = 1
  for (const s of students.value) {
    for (const sub of (s.subjects || [])) {
      const key = sub.code ?? sub.name ?? `subject-${autoIndex}`
      if (!map.has(key)) {
        map.set(key, {
          key,
          code: sub.code ?? null,
          name: sub.name ?? null,
          displayName: chooseSubjectDisplayName(sub, autoIndex),
        })
        autoIndex++
      } else {
        const entry = map.get(key)
        if (!entry.name && sub.name) entry.name = sub.name
        if (!entry.code && sub.code) entry.code = sub.code
        if (!entry.displayName || !/[A-Za-z]/.test(entry.displayName)) {
          entry.displayName = chooseSubjectDisplayName(sub, autoIndex)
        }
      }
    }
  }
  return Array.from(map.values())
})

const subjectNameLookup = computed(() => {
  const lookup = new Map()
  for (const col of subjectColumns.value) {
    if (col.code) lookup.set(col.code, col.displayName)
    if (col.name) lookup.set(col.name, col.displayName)
    lookup.set(col.key, col.displayName)
  }
  return lookup
})

function markFor(s, column) {
  const subjects = s.subjects || []
  let found = null
  if (column.code) {
    found = subjects.find(x => x.code === column.code)
  }
  if (!found && column.name) {
    const target = String(column.name).toLowerCase()
    found = subjects.find(x => String(x.name || '').toLowerCase() === target)
  }
  return found && typeof found.marks === 'number' ? found.marks : '-'
}
function totalFor(s) {
  let sum = 0
  for (const sub of (s.subjects || [])) { if (typeof sub.marks === 'number') sum += sub.marks }
  return sum
}
function avgFor(s) {
  let sum = 0, cnt = 0
  for (const sub of (s.subjects || [])) { if (typeof sub.marks === 'number') { sum += sub.marks; cnt++ } }
  return cnt ? Math.round(sum / cnt) : 0
}

function gradeFor(s) {
  const avg = avgFor(s)
  if (avg >= 75) return 'A'
  if (avg >= 65) return 'B'
  if (avg >= 55) return 'C'
  if (avg >= 40) return 'D'
  if (avg > 0) return 'F'
  return '-'
}

function passRateClass(rate) {
  if (rate >= 80) return 'bg-emerald-100 text-emerald-700'
  if (rate >= 60) return 'bg-sky-100 text-sky-700'
  if (rate >= 40) return 'bg-amber-100 text-amber-700'
  return 'bg-rose-100 text-rose-700'
}

async function loadExamMeta() {
  try {
    const res = await fetch(`/api/public/schools/${props.schoolId}/exams`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await res.json()
    meta.value = Array.isArray(data) ? data.find(e => e.id === props.examId) || null : null
  } catch (e) { meta.value = null }
}

async function loadResults() {
  loading.value = true
  try {
    const url = new URL(`/api/public/schools/${props.schoolId}/exams/${props.examId}/results`, window.location.origin)
    if (selectedClass.value) url.searchParams.set('class_name', selectedClass.value)
    else if (classes.value.length > 0) url.searchParams.set('class_name', classes.value[0])
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await res.json()
    schoolTitle.value = data?.school?.school_name || ''
    classes.value = Array.isArray(data?.classes) ? data.classes : []
    summary.value = data?.summary || { F: { I:0, II:0, III:0, IV:0, 0:0 }, M: { I:0, II:0, III:0, IV:0, 0:0 }, T: { I:0, II:0, III:0, IV:0, 0:0 } }
    students.value = Array.isArray(data?.students) ? data.students : []
  } finally { loading.value = false }
}

onMounted(async () => {
  // If URL has ?class_name=..., use it
  try {
    const params = new URLSearchParams(window.location.search)
    const cls = params.get('class_name')
    if (cls) selectedClass.value = cls
  } catch (e) {}
  await loadExamMeta()
  await loadResults()
  // Default to first class if none selected and classes list is available
  if (!selectedClass.value && classes.value.length > 0) {
    selectedClass.value = classes.value[0]
    await loadResults()
  }
})

function doPrint() { window.print() }

const subjectStats = computed(() => {
  const map = new Map()
  let autoIndex = 1
  for (const s of students.value) {
    for (const sub of (s.subjects || [])) {
      const key = sub.code ?? sub.name ?? `subject-${autoIndex}`
      if (!map.has(key)) {
        map.set(key, {
          key,
          code: sub.code ?? null,
          name: sub.name ?? sub.code ?? `Subject ${autoIndex}`,
          A:0,
          B:0,
          C:0,
          D:0,
          F:0,
          sum:0,
          cnt:0,
          pass:0,
          learners: new Set(),
          displayName: chooseSubjectDisplayName(sub, autoIndex),
        })
        autoIndex++
      }
      const rec = map.get(key)
      const grade = String(sub.grade || '').toUpperCase()
      if (grade === 'A') rec.A++
      else if (grade === 'B') rec.B++
      else if (grade === 'C') rec.C++
      else if (grade === 'D') rec.D++
      else rec.F++
      if (typeof sub.marks === 'number') { rec.sum += sub.marks; rec.cnt++ }
      if (typeof sub.marks === 'number' && sub.marks >= 40) rec.pass++
      if (s?.reg_no) rec.learners.add(s.reg_no)
    }
  }
  const rows = Array.from(map.values()).map((r) => {
    const candidates = r.learners.size || r.cnt
    const avg = r.cnt ? Math.round(r.sum / r.cnt) : 0
    const passRate = candidates ? Math.round((r.pass / candidates) * 100) : 0
    return {
      ...r,
      candidateCount: candidates,
      avg,
      passRate,
      failRate: candidates ? Math.round(((candidates - r.pass) / candidates) * 100) : 0,
    }
  })
  rows.sort((a,b) => {
    if (b.passRate === a.passRate) return b.avg - a.avg
    return b.passRate - a.passRate
  })
  return rows.map((r, idx) => ({ ...r, position: idx + 1 }))
})
</script>

<style scoped>
@media print {
  header, .print\:hidden, nav, .footer, footer { display: none !important; }
  .rounded-xl, .shadow-sm { box-shadow: none !important; border: 1px solid #ddd !important; }
  table { page-break-inside: avoid; }
}
</style>

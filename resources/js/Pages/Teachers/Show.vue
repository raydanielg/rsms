<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  teacher: { type: Object, required: true },
  students: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({ q: '', subject_id: null }) },
  exams: { type: Array, default: () => [] },
})

const q = ref(props.filters.q || '')
const subjectId = ref(props.filters.subject_id || '')

const subjectOptions = computed(() => (props.teacher.subjects || []).map(s => ({ id: s.id, name: s.name })))

function applyFilters() {
  router.get(route('teachers.show', { id: props.teacher.id }), {
    q: q.value || '',
    subject_id: subjectId.value || ''
  }, { preserveState: true, replace: true })
}

const paged = computed(() => props.students) // server sends already-filtered list for now

// Tabs
const tabs = ['students','results','more']
const active = ref('students')
function setTab(t){ if (tabs.includes(t)) active.value = t }

// More tab state
const teacherSubjectIds = computed(() => (props.teacher.subjects || []).map(s => s.id))
const year = ref('')
const klassId = ref('')
const examId = ref('')
const years = computed(() => Array.from(new Set((props.exams || []).map(e => e.year))).sort((a,b)=>b-a))
const classesOpts = computed(() => props.teacher.classes || [])
const examsForYear = computed(() => (props.exams || []).filter(e => !year.value || e.year === Number(year.value)))
const analysis = ref({ loaded: false, total: 0, pass: 0, perSubject: [] })
async function loadAnalysis(){
  analysis.value = { loaded: false, total: 0, pass: 0, perSubject: [] }
  if (!examId.value || !klassId.value) return
  const url = route('results.exams.marks', { examId: Number(examId.value) }) + `?class_id=${klassId.value}`
  const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
  if (!r.ok) { analysis.value = { loaded: true, total: 0, pass: 0, perSubject: [] }; return }
  const data = await r.json()
  const subs = Array.isArray(data?.subjects) ? data.subjects : []
  const students = Array.isArray(data?.students) ? data.students : []
  const marks = data?.marks || {}
  const allowedIds = new Set(teacherSubjectIds.value)
  const filteredSubs = subs.filter(s => allowedIds.has(s.id))
  const perSubject = []
  let total = 0, pass = 0
  for (const s of filteredSubs){
    let sum = 0, cnt = 0, passCnt = 0
    for (const st of students){
      const key = `${st.id}_${s.id}`
      const m = Number(marks[key] ?? NaN)
      if (!Number.isFinite(m)) continue
      sum += m; cnt += 1; if (m >= 40) passCnt += 1
      total += 1; if (m >= 40) pass += 1
    }
    const avg = cnt ? Math.round((sum/cnt)*10)/10 : 0
    perSubject.push({ id: s.id, name: s.name, avg, cnt, pass: passCnt })
  }
  analysis.value = { loaded: true, total, pass, perSubject }
}
</script>

<template>
  <Head :title="`Teacher • ${teacher.name}`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ teacher.name }}</h2>
          <div class="mt-1 text-sm text-gray-600">Check No: {{ teacher.check_no }} · Phone: {{ teacher.phone || '—' }} · Email: {{ teacher.email || '—' }}</div>
          <div class="mt-1 text-xs text-gray-500">Classes: {{ (teacher.classes || []).map(c => c.name).join(', ') || '—' }}</div>
        </div>
        <div class="flex items-center gap-2">
          <Link :href="route('teachers.index')" class="text-sm text-emerald-700 hover:text-emerald-900">← Back to teachers</Link>
        </div>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Tabs -->
      <div class="border-b">
        <nav class="-mb-px flex gap-6 text-sm">
          <button @click="setTab('students')" :class="[active==='students' ? 'border-b-2 border-emerald-600 font-medium text-emerald-700' : 'text-gray-500 hover:text-gray-700','px-1 py-2']">Students</button>
          <button @click="setTab('results')" :class="[active==='results' ? 'border-b-2 border-emerald-600 font-medium text-emerald-700' : 'text-gray-500 hover:text-gray-700','px-1 py-2']">Results</button>
          <button @click="setTab('more')" :class="[active==='more' ? 'border-b-2 border-emerald-600 font-medium text-emerald-700' : 'text-gray-500 hover:text-gray-700','px-1 py-2']">More</button>
        </nav>
      </div>

      <!-- Filters -->
      <div v-if="active==='students'" class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2">
          <input v-model="q" @keyup.enter="applyFilters" type="text" placeholder="Search reg no / name / class" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
          <select v-model="subjectId" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">All subjects</option>
            <option v-for="s in subjectOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
        <div class="flex shrink-0 items-center gap-2">
          <button @click="applyFilters" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Apply</button>
        </div>
      </div>

      <!-- Students table -->
      <div v-if="active==='students'" class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Reg No</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Full name</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Class</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Subjects</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="s in paged" :key="s.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">{{ s.reg_no }}</td>
                <td class="px-4 py-3 text-gray-800">{{ s.name }}</td>
                <td class="px-4 py-3 text-gray-700">{{ s.class_name }}</td>
                <td class="px-4 py-3">
                  <div class="flex max-w-[360px] flex-wrap gap-1">
                    <span v-for="sub in (s.subjects || [])" :key="sub.id" class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ sub.name }}</span>
                    <span v-if="!s.subjects || s.subjects.length===0" class="text-xs text-gray-500">—</span>
                  </div>
                </td>
              </tr>
              <tr v-if="!paged || paged.length===0">
                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No students match your filters.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Results tab: exam cards -->
      <div v-if="active==='results'" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="ex in exams" :key="ex.id" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-base font-semibold text-gray-900">{{ ex.name }}</div>
              <div class="text-xs text-gray-600">{{ ex.type }} · Year {{ ex.year }} · Term {{ ex.term }}</div>
            </div>
            <Link :href="route('teachers.exam', { id: teacher.id, examId: ex.id })" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100" aria-label="Open">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </Link>
          </div>
        </div>
        <div v-if="!exams || exams.length===0" class="col-span-full rounded-lg border border-dashed p-6 text-center text-sm text-gray-500">No exams found for these classes.</div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  teacher: { type: Object, required: true },
  exam: { type: Object, required: true },
})

const klassId = ref('')
const subjectFilter = ref('')
const loading = ref(false)
const error = ref('')
const matrix = ref({ subjects: [], students: [], marks: {} })

const teacherSubjectIds = computed(() => new Set((props.teacher.subjects || []).map(s => s.id)))
const classOptions = computed(() => props.teacher.classes || [])
const subjectOptions = computed(() => (matrix.value.subjects || []).filter(s => teacherSubjectIds.value.has(s.id)))

async function loadMatrix() {
  error.value = ''
  matrix.value = { subjects: [], students: [], marks: {} }
  if (!klassId.value) return
  loading.value = true
  try {
    const url = route('results.exams.marks', { examId: props.exam.id }) + `?class_id=${klassId.value}`
    const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    if (!r.ok) throw new Error('Failed to load')
    const data = await r.json()
    const subs = Array.isArray(data?.subjects) ? data.subjects : []
    const students = Array.isArray(data?.students) ? data.students : []
    const marks = data?.marks || {}
    // Keep only teacher's subjects
    const filteredSubs = subs.filter(s => teacherSubjectIds.value.has(s.id))
    matrix.value = { subjects: filteredSubs, students, marks }
  } catch (e) {
    error.value = 'Could not load results. Please try again.'
  } finally {
    loading.value = false
  }
}

// KPIs
const stats = computed(() => {
  const subs = matrix.value.subjects || []
  const students = matrix.value.students || []
  const marks = matrix.value.marks || {}
  const subId = subjectFilter.value ? Number(subjectFilter.value) : null
  let total = 0, pass = 0
  const perSubject = []
  for (const s of subs) {
    if (subId && s.id !== subId) continue
    let sum = 0, cnt = 0, passCnt = 0
    for (const st of students) {
      const key = `${st.id}_${s.id}`
      const m = Number(marks[key] ?? NaN)
      if (!Number.isFinite(m)) continue
      sum += m; cnt += 1; if (m >= 40) passCnt += 1
      total += 1; if (m >= 40) pass += 1
    }
    perSubject.push({ id: s.id, name: s.name, avg: cnt ? Math.round((sum/cnt)*10)/10 : 0, cnt, pass: passCnt })
  }
  // Sort by avg desc for chart
  perSubject.sort((a,b)=>b.avg-a.avg)
  return { total, pass, perSubject }
})

// Pie chart geometry
const pie = computed(() => {
  const total = stats.value.total || 1
  const pass = stats.value.pass
  const fail = total - pass
  const passAngle = (pass/total) * 2*Math.PI
  function arcPath(cx, cy, r, start, end) {
    const x1 = cx + r * Math.cos(start), y1 = cy + r * Math.sin(start)
    const x2 = cx + r * Math.cos(end),   y2 = cy + r * Math.sin(end)
    const large = end-start > Math.PI ? 1 : 0
    return `M ${cx} ${cy} L ${x1} ${y1} A ${r} ${r} 0 ${large} 1 ${x2} ${y2} Z`
  }
  return {
    passPath: arcPath(60,60,50,0,passAngle),
    failPath: arcPath(60,60,50,passAngle,2*Math.PI),
    passPct: Math.round((pass/total)*100),
  }
})

// Bar chart data
const bars = computed(() => {
  const items = stats.value.perSubject
  const maxAvg = Math.max(100, ...items.map(i=>i.avg))
  return items.map((i, idx) => ({
    name: i.name,
    width: Math.max(0, (i.avg/maxAvg)*100),
    value: i.avg,
    idx,
  }))
})

watch(klassId, loadMatrix)
</script>

<template>
  <Head :title="`Exam • ${exam.name} • ${teacher.name}`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ teacher.name }} — {{ exam.name }}</h2>
          <div class="mt-1 text-sm text-gray-600">Type: {{ exam.type }} · Year {{ exam.year }} · Term {{ exam.term }}</div>
        </div>
        <div class="flex items-center gap-2">
          <Link :href="route('teachers.show', { id: teacher.id })" class="text-sm text-emerald-700 hover:text-emerald-900">← Back to teacher</Link>
        </div>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Filters row -->
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-3">
          <select v-model="klassId" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">Select class...</option>
            <option v-for="c in classOptions" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
          <select v-model="subjectFilter" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">All subjects</option>
            <option v-for="s in subjectOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
          <button @click="loadMatrix" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Load</button>
        </div>
      </div>

      <!-- KPI + charts -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-lg border bg-white p-4 shadow-sm">
          <div class="text-sm font-medium text-gray-600">Pass rate</div>
          <div class="mt-3 flex items-center gap-4">
            <svg viewBox="0 0 120 120" width="120" height="120">
              <path :d="pie.failPath" fill="#fee2e2" />
              <path :d="pie.passPath" fill="#34d399" />
            </svg>
            <div>
              <div class="text-2xl font-semibold text-gray-900">{{ pie.passPct }}%</div>
              <div class="text-xs text-gray-500">of {{ stats.total }} marks</div>
            </div>
          </div>
        </div>
        <div class="md:col-span-2 rounded-lg border bg-white p-4 shadow-sm">
          <div class="text-sm font-medium text-gray-600">Average by subject</div>
          <div class="mt-3 space-y-2">
            <div v-for="b in bars" :key="b.idx" class="flex items-center gap-3">
              <div class="w-40 truncate text-xs text-gray-700">{{ b.name }}</div>
              <div class="h-3 w-full rounded bg-gray-100">
                <div class="h-3 rounded bg-emerald-500" :style="{ width: b.width + '%' }"></div>
              </div>
              <div class="w-10 text-right text-xs text-gray-700">{{ b.value }}</div>
            </div>
            <div v-if="bars.length===0" class="text-xs text-gray-500">No data</div>
          </div>
        </div>
      </div>

      <!-- Results table -->
      <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Reg No</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Student</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Subject</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Marks</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <template v-for="st in matrix.students" :key="st.id">
                <template v-for="s in matrix.subjects" :key="s.id">
                  <tr v-if="!subjectFilter || Number(subjectFilter)===s.id">
                    <td class="px-4 py-2 text-gray-900">{{ st.reg_no }}</td>
                    <td class="px-4 py-2 text-gray-800">{{ st.name }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ s.name }}</td>
                    <td class="px-4 py-2 text-gray-900">
                      {{ Number(matrix.marks[`${st.id}_${s.id}`] ?? NaN) || '' }}
                    </td>
                  </tr>
                </template>
              </template>
              <tr v-if="(matrix.students||[]).length===0 || (matrix.subjects||[]).length===0">
                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">Select class and click Load to view results.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-if="error" class="rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">{{ error }}</div>
    </div>
  </AuthenticatedLayout>
</template>

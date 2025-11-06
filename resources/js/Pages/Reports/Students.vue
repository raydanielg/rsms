<script setup>
import { ref, onMounted, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const data = ref({ total: 0, male: 0, female: 0, by_class: [] });
const topStudents = ref([]);
const subjectRankings = ref([]);
const filters = ref({ exams: [], classes: [], subjects: [] });
const loading = ref(false);
const err = ref('');

// Filter selections
const selectedExam = ref('');
const selectedClass = ref('');
const selectedSubject = ref('');
const topLimit = ref(10);

async function loadFilters() {
  try {
    const res = await fetch(route('api.reports.filters'), { 
      headers: { 'X-Requested-With': 'XMLHttpRequest' } 
    });
    if (res.ok) {
      filters.value = await res.json();
    }
  } catch (e) {
    console.error('Failed to load filters');
  }
}

async function load() {
  loading.value = true;
  err.value = '';
  try {
    const res = await fetch(route('api.reports.students'), { 
      headers: { 'X-Requested-With': 'XMLHttpRequest' } 
    });
    if (!res.ok) throw new Error('Failed');
    data.value = await res.json();
  } catch (e) {
    err.value = 'Failed to load students report.';
  } finally {
    loading.value = false;
  }
}

async function loadTopStudents() {
  try {
    const params = new URLSearchParams();
    if (selectedExam.value) params.append('exam_id', selectedExam.value);
    if (selectedClass.value) params.append('class_name', selectedClass.value);
    if (selectedSubject.value) params.append('subject_id', selectedSubject.value);
    params.append('limit', topLimit.value);

    const res = await fetch(route('api.reports.top-students') + '?' + params.toString(), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (res.ok) {
      const result = await res.json();
      topStudents.value = result.students || [];
    }
  } catch (e) {
    console.error('Failed to load top students');
  }
}

async function loadSubjectRankings() {
  try {
    const params = new URLSearchParams();
    if (selectedExam.value) params.append('exam_id', selectedExam.value);
    if (selectedClass.value) params.append('class_name', selectedClass.value);

    const res = await fetch(route('api.reports.subject-rankings') + '?' + params.toString(), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (res.ok) {
      const result = await res.json();
      subjectRankings.value = result.subjects || [];
    }
  } catch (e) {
    console.error('Failed to load subject rankings');
  }
}

onMounted(async () => {
  await loadFilters();
  await load();
  await loadTopStudents();
  await loadSubjectRankings();
});

watch([selectedExam, selectedClass, selectedSubject, topLimit], () => {
  loadTopStudents();
  loadSubjectRankings();
});
</script>

<template>
  <Head title="Students Reports" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Students Reports</h2>
    </template>

    <div class="space-y-6">
      <div v-if="err" class="rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">
        {{ err }}
        <button @click="load" class="ml-2 rounded border border-rose-300 px-2 py-0.5 text-rose-700 hover:bg-rose-100">
          Retry
        </button>
      </div>

      <!-- Summary cards -->
      <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-blue-600">Total Students</div>
              <div class="mt-2 text-3xl font-bold text-blue-900">{{ data.total }}</div>
            </div>
            <div class="rounded-full bg-blue-100 p-3">
              <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-green-100 bg-gradient-to-br from-green-50 to-white p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-green-600">Male Students</div>
              <div class="mt-2 text-3xl font-bold text-green-900">{{ data.male }}</div>
            </div>
            <div class="rounded-full bg-green-100 p-3">
              <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-pink-100 bg-gradient-to-br from-pink-50 to-white p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-pink-600">Female Students</div>
              <div class="mt-2 text-3xl font-bold text-pink-900">{{ data.female }}</div>
            </div>
            <div class="rounded-full bg-pink-100 p-3">
              <svg class="h-8 w-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <h3 class="mb-4 text-base font-semibold text-gray-800">Filters</h3>
        <div class="grid gap-4 sm:grid-cols-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Exam</label>
            <select v-model="selectedExam" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
              <option value="">All Exams</option>
              <option v-for="exam in filters.exams" :key="exam.id" :value="exam.id">{{ exam.name }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Class</label>
            <select v-model="selectedClass" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
              <option value="">All Classes</option>
              <option v-for="cls in filters.classes" :key="cls.id" :value="cls.name">{{ cls.name }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Subject</label>
            <select v-model="selectedSubject" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
              <option value="">All Subjects</option>
              <option v-for="subj in filters.subjects" :key="subj.id" :value="subj.id">{{ subj.name }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Top N</label>
            <input v-model.number="topLimit" type="number" min="5" max="50" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" />
          </div>
        </div>
      </div>

      <!-- Top Students -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b bg-gradient-to-r from-yellow-50 to-amber-50 px-6 py-4">
          <h3 class="text-lg font-semibold text-gray-800">🏆 Top Performing Students</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Rank</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Student Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Class</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Avg Marks</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Subjects</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="(student, idx) in topStudents" :key="student.id" class="hover:bg-gray-50 transition-colors">
                <td class="whitespace-nowrap px-6 py-4">
                  <span v-if="idx === 0" class="text-2xl">🥇</span>
                  <span v-else-if="idx === 1" class="text-2xl">🥈</span>
                  <span v-else-if="idx === 2" class="text-2xl">🥉</span>
                  <span v-else class="text-sm font-medium text-gray-700">{{ idx + 1 }}</span>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                  {{ student.name }}
                </td>
                <td class="whitespace-nowrap px-6 py-4">
                  <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                    {{ student.class_name }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-6 py-4">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-sm font-semibold"
                    :class="student.avg_marks >= 80 ? 'bg-green-100 text-green-800' : student.avg_marks >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800'">
                    {{ parseFloat(student.avg_marks).toFixed(1) }}%
                  </span>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                  {{ student.subjects_count }}
                </td>
              </tr>
              <tr v-if="topStudents.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                  No students found
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Subject Rankings -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4">
          <h3 class="text-lg font-semibold text-gray-800">📊 Subject Performance Rankings</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Avg Marks</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Max</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Min</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">Students</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="subj in subjectRankings" :key="subj.id" class="hover:bg-gray-50 transition-colors">
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                  {{ subj.name }}
                </td>
                <td class="whitespace-nowrap px-6 py-4">
                  <div class="flex items-center gap-2">
                    <div class="h-2 w-24 rounded-full bg-gray-200">
                      <div class="h-2 rounded-full" 
                        :class="subj.avg_marks >= 80 ? 'bg-green-500' : subj.avg_marks >= 60 ? 'bg-yellow-500' : 'bg-orange-500'"
                        :style="{ width: Math.min(100, subj.avg_marks) + '%' }">
                      </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ parseFloat(subj.avg_marks).toFixed(1) }}%</span>
                  </div>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-green-600 font-medium">
                  {{ parseFloat(subj.max_marks).toFixed(1) }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-orange-600 font-medium">
                  {{ parseFloat(subj.min_marks).toFixed(1) }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                  {{ subj.students_count }}
                </td>
              </tr>
              <tr v-if="subjectRankings.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                  No subject data found
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- By class distribution -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b bg-gradient-to-r from-teal-50 to-cyan-50 px-6 py-4">
          <h3 class="text-lg font-semibold text-gray-800">Student Distribution by Class</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left font-semibold text-gray-700">Class</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-700">Students</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-700">Distribution</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="row in data.by_class" :key="row.class_name" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-900 font-medium">{{ row.class_name || '—' }}</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">{{ row.count }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <div class="h-2 flex-1 max-w-xs rounded-full bg-gray-200">
                      <div class="h-2 rounded-full bg-blue-500" 
                        :style="{ width: Math.min(100, (row.count / data.total) * 100) + '%' }">
                      </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ ((row.count / data.total) * 100).toFixed(1) }}%</span>
                  </div>
                </td>
              </tr>
              <tr v-if="(data.by_class || []).length===0">
                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">No data.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

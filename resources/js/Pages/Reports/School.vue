<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const data = ref({ 
  students: 0, 
  classes: 0, 
  subjects: 0, 
  exams: 0, 
  teachers: 0, 
  marks: 0, 
  avg_subjects_per_class: 0,
  avg_performance: 0,
  pass_rate: 0
});
const coverage = ref([]);
const loading = ref(false);
const err = ref('');

async function load() {
  loading.value = true;
  err.value = '';
  try {
    const [a, b] = await Promise.all([
      fetch(route('api.reports.school'), { headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
      fetch(route('api.reports.classes'), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    ]);
    if (!a.ok || !b.ok) throw new Error('Failed');
    data.value = await a.json();
    const bc = await b.json();
    coverage.value = bc.classes || [];
  } catch (e) {
    err.value = 'Failed to load school report.';
  } finally {
    loading.value = false;
  }
}

onMounted(load);
</script>

<template>
  <Head title="School Report" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">School Report</h2>
    </template>

    <div class="space-y-6">
      <div v-if="err" class="rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">
        {{ err }}
        <button @click="load" class="ml-2 rounded border border-rose-300 px-2 py-0.5 text-rose-700 hover:bg-rose-100">
          Retry
        </button>
      </div>

      <!-- School Overview -->
      <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 shadow-md">
        <div class="mb-4 flex items-center gap-3">
          <div class="rounded-full bg-blue-500 p-3">
            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-blue-900">School Overview</h2>
            <p class="text-sm text-blue-700">Comprehensive school analytics and performance metrics</p>
          </div>
        </div>
      </div>

      <!-- Key Statistics Grid -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-blue-100 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-gray-600">Total Students</div>
              <div class="mt-2 text-3xl font-bold text-blue-600">{{ data.students }}</div>
            </div>
            <div class="rounded-full bg-blue-100 p-3">
              <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-purple-100 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-gray-600">Teachers</div>
              <div class="mt-2 text-3xl font-bold text-purple-600">{{ data.teachers }}</div>
            </div>
            <div class="rounded-full bg-purple-100 p-3">
              <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-green-100 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-gray-600">Classes</div>
              <div class="mt-2 text-3xl font-bold text-green-600">{{ data.classes }}</div>
            </div>
            <div class="rounded-full bg-green-100 p-3">
              <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-orange-100 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-gray-600">Subjects</div>
              <div class="mt-2 text-3xl font-bold text-orange-600">{{ data.subjects }}</div>
            </div>
            <div class="rounded-full bg-orange-100 p-3">
              <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Metrics -->
      <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-teal-100 bg-white p-6 shadow-sm">
          <div class="mb-3 flex items-center gap-2">
            <div class="rounded-full bg-teal-100 p-2">
              <svg class="h-5 w-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-sm font-medium text-gray-700">Pass Rate</div>
          </div>
          <div class="text-3xl font-bold text-teal-600">{{ data.pass_rate }}%</div>
          <div class="mt-2 h-2 w-full rounded-full bg-gray-200">
            <div class="h-2 rounded-full bg-teal-500 transition-all" :style="{ width: data.pass_rate + '%' }"></div>
          </div>
        </div>

        <div class="rounded-xl border border-indigo-100 bg-white p-6 shadow-sm">
          <div class="mb-3 flex items-center gap-2">
            <div class="rounded-full bg-indigo-100 p-2">
              <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div class="text-sm font-medium text-gray-700">Avg Performance</div>
          </div>
          <div class="text-3xl font-bold text-indigo-600">{{ data.avg_performance }}%</div>
          <div class="mt-2 text-xs text-gray-500">Overall average marks</div>
        </div>

        <div class="rounded-xl border border-rose-100 bg-white p-6 shadow-sm">
          <div class="mb-3 flex items-center gap-2">
            <div class="rounded-full bg-rose-100 p-2">
              <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
              </svg>
            </div>
            <div class="text-sm font-medium text-gray-700">Total Exams</div>
          </div>
          <div class="text-3xl font-bold text-rose-600">{{ data.exams }}</div>
          <div class="mt-2 text-xs text-gray-500">{{ data.marks }} marks recorded</div>
        </div>
      </div>

      <!-- Resource Utilization -->
      <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
          <div class="mb-4 flex items-center gap-2">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-800">Resource Ratios</h3>
          </div>
          <div class="space-y-4">
            <div>
              <div class="mb-1 flex justify-between text-sm">
                <span class="text-gray-600">Students per Class</span>
                <span class="font-semibold text-gray-900">{{ data.classes > 0 ? (data.students / data.classes).toFixed(1) : 0 }}</span>
              </div>
              <div class="h-2 w-full rounded-full bg-gray-200">
                <div class="h-2 rounded-full bg-blue-500" :style="{ width: Math.min(100, (data.students / data.classes / 50) * 100) + '%' }"></div>
              </div>
            </div>
            <div>
              <div class="mb-1 flex justify-between text-sm">
                <span class="text-gray-600">Students per Teacher</span>
                <span class="font-semibold text-gray-900">{{ data.teachers > 0 ? (data.students / data.teachers).toFixed(1) : 0 }}</span>
              </div>
              <div class="h-2 w-full rounded-full bg-gray-200">
                <div class="h-2 rounded-full bg-purple-500" :style="{ width: Math.min(100, (data.students / data.teachers / 30) * 100) + '%' }"></div>
              </div>
            </div>
            <div>
              <div class="mb-1 flex justify-between text-sm">
                <span class="text-gray-600">Avg Subjects per Class</span>
                <span class="font-semibold text-gray-900">{{ data.avg_subjects_per_class }}</span>
              </div>
              <div class="h-2 w-full rounded-full bg-gray-200">
                <div class="h-2 rounded-full bg-green-500" :style="{ width: Math.min(100, (data.avg_subjects_per_class / 15) * 100) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
          <div class="mb-4 flex items-center gap-2">
            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-800">Quick Stats</h3>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="rounded-lg bg-blue-50 p-4">
              <div class="text-xs text-blue-600">Student-Teacher Ratio</div>
              <div class="mt-1 text-xl font-bold text-blue-900">{{ data.teachers > 0 ? (data.students / data.teachers).toFixed(0) : 0 }}:1</div>
            </div>
            <div class="rounded-lg bg-green-50 p-4">
              <div class="text-xs text-green-600">Class Size</div>
              <div class="mt-1 text-xl font-bold text-green-900">{{ data.classes > 0 ? (data.students / data.classes).toFixed(0) : 0 }}</div>
            </div>
            <div class="rounded-lg bg-purple-50 p-4">
              <div class="text-xs text-purple-600">Subjects/Class</div>
              <div class="mt-1 text-xl font-bold text-purple-900">{{ data.avg_subjects_per_class }}</div>
            </div>
            <div class="rounded-lg bg-orange-50 p-4">
              <div class="text-xs text-orange-600">Total Marks</div>
              <div class="mt-1 text-xl font-bold text-orange-900">{{ data.marks }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Class Subject Coverage -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b bg-gradient-to-r from-cyan-50 to-blue-50 px-6 py-4">
          <h3 class="text-lg font-semibold text-gray-800">Class Subject Coverage</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left font-semibold text-gray-700">Class Name</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-700">Assigned Subjects</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-700">Coverage</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="c in coverage" :key="c.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-medium text-gray-900">{{ c.name }}</td>
                <td class="px-6 py-4 text-gray-700 font-semibold">{{ c.subjects }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <div class="h-2 flex-1 max-w-xs rounded-full bg-gray-200">
                      <div class="h-2 rounded-full bg-cyan-500" 
                        :style="{ width: Math.min(100, (c.subjects / data.subjects) * 100) + '%' }">
                      </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ data.subjects > 0 ? ((c.subjects / data.subjects) * 100).toFixed(0) : 0 }}%</span>
                  </div>
                </td>
              </tr>
              <tr v-if="coverage.length === 0">
                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">No data available</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

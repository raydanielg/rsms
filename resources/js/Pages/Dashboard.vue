<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const years = [2022, 2023, 2024, 2025];
const selectedYear = ref(2025);

// Demo data; replace with server-provided props when ready
const base = {
  2022: { students: 820, teachers: 54, subjects: 18, classes: 24, parents: 600 },
  2023: { students: 910, teachers: 59, subjects: 19, classes: 26, parents: 680 },
  2024: { students: 980, teachers: 63, subjects: 20, classes: 28, parents: 740 },
  2025: { students: 1050, teachers: 67, subjects: 22, classes: 30, parents: 800 },
};
const stats = computed(() => base[selectedYear.value]);

const pieData = computed(() => ({
  attendance: { present: 78, absent: 22 },
  results: { pass: 68, fail: 32 },
}));

const activities = ref([
  { time: '10:12', text: 'New student registered: Asha M.' },
  { time: '09:55', text: 'Marks uploaded for Form 2 Mathematics.' },
  { time: '09:20', text: 'Class timetables updated.' },
  { time: 'Yesterday', text: 'Teacher John K. added a lesson plan.' },
]);

function pieStyle(parts) {
  const total = Object.values(parts).reduce((a, b) => a + b, 0);
  const first = Math.round((Object.values(parts)[0] / total) * 360);
  return {
    background: `conic-gradient(#16a34a 0deg ${first}deg, #ef4444 ${first}deg 360deg)`,
  };
}
</script>

<template>
  <Head title="RSMS" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">RSMS</h2>
    </template>

    <div class="space-y-6">
      <!-- Filters -->
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-gray-600">Overview</div>
        <div class="flex items-center gap-2">
          <label for="year" class="text-sm text-gray-600">Year</label>
          <select id="year" v-model="selectedYear" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border border-green-100 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-500">Total Students</div>
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.students }}</div>
            </div>
            <div class="rounded-lg bg-green-50 p-3 text-green-600">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5 12.083 12.083 0 015.84 10.578L12 14z"/></svg>
            </div>
          </div>
        </div>
        <div class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-500">Total Teachers</div>
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.teachers }}</div>
            </div>
            <div class="rounded-lg bg-blue-50 p-3 text-blue-700">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.127a9.024 9.024 0 006 0V18a6 6 0 10-12 0v1.127a9.024 9.024 0 006 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>
            </div>
          </div>
        </div>
        <div class="rounded-xl border border-yellow-100 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-500">Total Subjects</div>
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.subjects }}</div>
            </div>
            <div class="rounded-lg bg-yellow-50 p-3 text-yellow-700">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253l7.071 4.084-7.07 4.084L4.93 10.337 12 6.253z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14.421l7.5-4.329V18L12 22.5 4.5 18v-7.908L12 14.42z"/></svg>
            </div>
          </div>
        </div>
        <div class="rounded-xl border border-purple-100 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-500">Total Classes</div>
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.classes }}</div>
            </div>
            <div class="rounded-lg bg-purple-50 p-3 text-purple-700">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A1.5 1.5 0 014.5 6h15A1.5 1.5 0 0121 7.5v9A1.5 1.5 0 0119.5 18H4.5A1.5 1.5 0 013 16.5v-9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18"/></svg>
            </div>
          </div>
        </div>
        <div class="rounded-xl border border-rose-100 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-500">Total Parents</div>
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.parents }}</div>
            </div>
            <div class="rounded-lg bg-rose-50 p-3 text-rose-700">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 10-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z"/></svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts and activity -->
      <div class="grid gap-4 lg:grid-cols-3">
        <!-- Pie charts -->
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm lg:col-span-2">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800">Analytics</h3>
            <div class="text-xs text-gray-500">Year {{ selectedYear }}</div>
          </div>
          <div class="grid gap-6 sm:grid-cols-2">
            <div class="flex items-center gap-4">
              <div class="h-28 w-28 flex-none rounded-full" :style="pieStyle(pieData.attendance)"></div>
              <div>
                <div class="text-sm font-medium text-gray-800">Attendance</div>
                <div class="mt-2 space-y-1 text-sm">
                  <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-green-600"></span> Present: {{ pieData.attendance.present }}%</div>
                  <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-red-500"></span> Absent: {{ pieData.attendance.absent }}%</div>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <div class="h-28 w-28 flex-none rounded-full" :style="pieStyle(pieData.results)"></div>
              <div>
                <div class="text-sm font-medium text-gray-800">Results</div>
                <div class="mt-2 space-y-1 text-sm">
                  <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-green-600"></span> Pass: {{ pieData.results.pass }}%</div>
                  <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-red-500"></span> Fail: {{ pieData.results.fail }}%</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Last activity -->
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800">Last Activity</h3>
            <a href="#" class="text-xs text-green-700 hover:underline">View all</a>
          </div>
          <ul class="space-y-3">
            <li v-for="(a, idx) in activities" :key="idx" class="flex items-start gap-3">
              <span class="mt-1 inline-block h-2 w-2 flex-none rounded-full bg-green-600"></span>
              <div>
                <div class="text-sm text-gray-800">{{ a.text }}</div>
                <div class="text-xs text-gray-500">{{ a.time }}</div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
  selected_year: { type: Number, required: true },
  years: { type: Array, default: () => [] },
  stats: { type: Object, default: () => ({}) },
  results: { type: Object, default: () => ({ pass: 0, fail: 0, total_marks: 0 }) },
  activities: { type: Array, default: () => [] },
  charts: { type: Object, default: () => ({ teachers: [], subjects: [], gender: [] }) },
});

const selectedYear = ref(props.selected_year);
function applyYear() {
  router.get(route('dashboard'), { year: selectedYear.value }, { preserveState: true, replace: true });
}

watch(() => props.selected_year, (val) => {
  if (val !== undefined && val !== null) {
    selectedYear.value = val;
  }
});

function pieStyle(pass, fail) {
  const total = Math.max(1, Number(pass) + Number(fail));
  const passDeg = Math.round((Number(pass) / total) * 360);
  return {
    background: `conic-gradient(#16a34a 0deg ${passDeg}deg, #ef4444 ${passDeg}deg 360deg)`,
  };
}

const teacherBars = computed(() => (props.charts.teachers || []).map((t, idx) => ({
  label: t.name,
  value: t.pass_rate,
  idx,
})));
const subjectBars = computed(() => (props.charts.subjects || []).map((s, idx) => ({
  label: s.name,
  value: s.avg,
  idx,
})));
const genderBars = computed(() => (props.charts.gender || []).map((g, idx) => ({
  label: g.sex,
  value: g.pass_rate,
  idx,
})));
const classGender = computed(() => props.charts.class_gender || []);

function classGenderStyle(male, female) {
  const m = Number(male) || 0;
  const f = Number(female) || 0;
  const total = Math.max(1, m + f);
  const maleDeg = Math.round((m / total) * 360);
  return {
    background: `conic-gradient(#60a5fa 0deg ${maleDeg}deg, #f472b6 ${maleDeg}deg 360deg)`,
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
          <select id="year" v-model="selectedYear" @change="applyYear" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
            <option v-for="y in props.years" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border border-green-100 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-500">Total Students</div>
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ props.stats.students }}</div>
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
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ props.stats.teachers }}</div>
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
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ props.stats.subjects }}</div>
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
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ props.stats.classes }}</div>
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
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ props.stats.parents }}</div>
            </div>
            <div class="rounded-lg bg-rose-50 p-3 text-rose-700">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 10-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z"/></svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts and activity -->
      <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
          <!-- Results analytics (DB-backed) -->
          <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h3 class="text-base font-semibold text-gray-800">Results Analytics</h3>
                <div class="text-xs text-gray-500">Year {{ selectedYear }}</div>
              </div>
              <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span> Pass: {{ props.results.pass }}%</span>
                <span class="inline-flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-red-500"></span> Fail: {{ props.results.fail }}%</span>
              </div>
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
              <div class="flex items-center gap-4">
                <div class="h-28 w-28 flex-none rounded-full" :style="pieStyle(props.results.pass, props.results.fail)"></div>
                <div>
                  <div class="text-sm font-medium text-gray-800">Pass/Fail</div>
                  <div class="mt-2 space-y-1 text-sm">
                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span> Pass: {{ props.results.pass }}%</div>
                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-red-500"></span> Fail: {{ props.results.fail }}%</div>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div>
                  <div class="text-sm font-medium text-gray-800">Totals</div>
                  <div class="mt-2 space-y-1 text-sm text-gray-700">
                    <div>Total marks counted: {{ props.results.total_marks }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-6">
              <div class="text-sm font-medium text-gray-800">Gender per class</div>
              <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <div v-for="item in classGender" :key="item.class" class="flex items-center gap-4 rounded-lg border border-gray-100 p-3">
                  <div class="h-20 w-20 flex-none rounded-full" :style="classGenderStyle(item.male, item.female)"></div>
                  <div class="space-y-1 text-sm text-gray-700">
                    <div class="font-semibold text-gray-900">{{ item.class }}</div>
                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-blue-400"></span> Boys: {{ item.male }}</div>
                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-pink-400"></span> Girls: {{ item.female }}</div>
                  </div>
                </div>
                <div v-if="!classGender.length" class="col-span-full rounded-lg border border-dashed p-4 text-sm text-gray-500">No class gender data yet.</div>
              </div>
            </div>
          </div>

          <!-- Recent activity -->
          <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-base font-semibold text-gray-800">Recent Activity</h3>
            </div>
            <ul class="space-y-3">
              <li v-for="(a, idx) in props.activities" :key="idx" class="flex items-start gap-3">
                <span class="mt-1 inline-block h-2 w-2 flex-none rounded-full bg-green-600"></span>
                <div>
                  <div class="text-sm text-gray-800">{{ a.text }}</div>
                  <div class="text-xs text-gray-500">{{ a.meta }}</div>
                </div>
              </li>
              <li v-if="!props.activities || props.activities.length===0" class="text-xs text-gray-500">No recent activity.</li>
            </ul>
          </div>
        </div>

        <div class="space-y-4">
          <!-- Teacher pass rate chart -->
          <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-base font-semibold text-gray-800">Teacher Pass Rate</h3>
              <div class="text-xs text-gray-500">Top {{ teacherBars.length }}</div>
            </div>
            <div class="space-y-2">
              <div v-for="bar in teacherBars" :key="bar.idx" class="flex items-center gap-3">
                <div class="w-32 truncate text-xs text-gray-700">{{ bar.label }}</div>
                <div class="h-3 w-full rounded bg-gray-100">
                  <div class="h-3 rounded bg-emerald-500" :style="{ width: Math.min(100, bar.value) + '%' }"></div>
                </div>
                <div class="w-10 text-right text-xs text-gray-700">{{ bar.value }}%</div>
              </div>
              <div v-if="teacherBars.length===0" class="text-xs text-gray-500">No data.</div>
            </div>
          </div>

          <!-- Subject average chart -->
          <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-base font-semibold text-gray-800">Subject Average</h3>
              <div class="text-xs text-gray-500">Top {{ subjectBars.length }}</div>
            </div>
            <div class="space-y-2">
              <div v-for="bar in subjectBars" :key="bar.idx" class="flex items-center gap-3">
                <div class="w-32 truncate text-xs text-gray-700">{{ bar.label }}</div>
                <div class="h-3 w-full rounded bg-gray-100">
                  <div class="h-3 rounded bg-blue-500" :style="{ width: Math.min(100, bar.value) + '%' }"></div>
                </div>
                <div class="w-10 text-right text-xs text-gray-700">{{ bar.value }}</div>
              </div>
              <div v-if="subjectBars.length===0" class="text-xs text-gray-500">No data.</div>
            </div>
          </div>

          <!-- Gender pass rate -->
          <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-base font-semibold text-gray-800">Gender Pass Rate</h3>
            </div>
            <div class="space-y-2">
              <div v-for="bar in genderBars" :key="bar.idx" class="flex items-center gap-3">
                <div class="w-24 text-xs font-medium text-gray-700">{{ bar.label }}</div>
                <div class="h-3 w-full rounded bg-gray-100">
                  <div class="h-3 rounded bg-purple-500" :style="{ width: Math.min(100, bar.value) + '%' }"></div>
                </div>
                <div class="w-10 text-right text-xs text-gray-700">{{ bar.value }}%</div>
              </div>
              <div v-if="genderBars.length===0" class="text-xs text-gray-500">No data.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

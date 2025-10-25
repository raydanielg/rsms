<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const data = ref({ students: 0, classes: 0, subjects: 0, exams: 0, marks: 0, avg_subjects_per_class: 0 });
const coverage = ref([]);
const loading = ref(false);
const err = ref('');

async function load() {
  loading.value = true; err.value = '';
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
  } finally { loading.value = false; }
}

onMounted(load);
</script>

<template>
  <Head title="School Report" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">School Report</h2>
    </template>

    <div class="space-y-4">
      <div v-if="err" class="rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ err }} <button @click="load" class="ml-2 rounded border border-rose-300 px-2 py-0.5 text-rose-700 hover:bg-rose-100">Retry</button></div>

      <!-- Summary cards -->
      <div class="grid gap-3 sm:grid-cols-5">
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Students</div>
          <div class="mt-1 text-2xl font-semibold text-gray-800">{{ data.students }}</div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Classes</div>
          <div class="mt-1 text-2xl font-semibold text-gray-800">{{ data.classes }}</div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Subjects</div>
          <div class="mt-1 text-2xl font-semibold text-gray-800">{{ data.subjects }}</div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Exams</div>
          <div class="mt-1 text-2xl font-semibold text-gray-800">{{ data.exams }}</div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Avg subjects/class</div>
          <div class="mt-1 text-2xl font-semibold text-gray-800">{{ data.avg_subjects_per_class }}</div>
        </div>
      </div>

      <!-- Classes coverage -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-700">Subjects per class</div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Class</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Assigned subjects</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="c in coverage" :key="c.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-900">{{ c.name }}</td>
                <td class="px-4 py-3 text-gray-700">{{ c.subjects }}</td>
              </tr>
              <tr v-if="coverage.length===0">
                <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">No data.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

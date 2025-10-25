<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const data = ref({ total: 0, male: 0, female: 0, by_class: [] });
const loading = ref(false);
const err = ref('');

async function load() {
  loading.value = true; err.value = '';
  try {
    const res = await fetch(route('api.reports.students'), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error('Failed');
    data.value = await res.json();
  } catch (e) {
    err.value = 'Failed to load students report.';
  } finally { loading.value = false; }
}

onMounted(load);
</script>

<template>
  <Head title="Students Reports" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Students Reports</h2>
    </template>

    <div class="space-y-4">
      <div v-if="err" class="rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ err }} <button @click="load" class="ml-2 rounded border border-rose-300 px-2 py-0.5 text-rose-700 hover:bg-rose-100">Retry</button></div>

      <!-- Summary cards -->
      <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Total students</div>
          <div class="mt-1 text-2xl font-semibold text-gray-800">{{ data.total }}</div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Male</div>
          <div class="mt-1 text-2xl font-semibold text-gray-800">{{ data.male }}</div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="text-xs text-gray-500">Female</div>
          <div class="mt-1 text-2xl font-semibold text-gray-800">{{ data.female }}</div>
        </div>
      </div>

      <!-- By class table -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Class</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Students</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="row in data.by_class" :key="row.class_name" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-900">{{ row.class_name || '—' }}</td>
                <td class="px-4 py-3 text-gray-700">{{ row.count }}</td>
              </tr>
              <tr v-if="(data.by_class || []).length===0">
                <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">No data.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

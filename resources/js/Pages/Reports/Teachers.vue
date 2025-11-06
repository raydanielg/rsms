<script setup>
import { ref, onMounted, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const data = ref({ total: 0, male: 0, female: 0, by_subject: [], by_class: [] });
const loading = ref(false);
const err = ref('');
const searchQuery = ref('');
const selectedSubject = ref('');

async function load() {
  loading.value = true;
  err.value = '';
  try {
    const res = await fetch(route('api.reports.teachers'), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!res.ok) throw new Error('Failed');
    data.value = await res.json();
  } catch (e) {
    err.value = 'Failed to load teachers report.';
  } finally {
    loading.value = false;
  }
}

const filteredByClass = computed(() => {
  let filtered = data.value.by_class || [];
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      row => 
        row.class_name?.toLowerCase().includes(query) ||
        row.teacher_name?.toLowerCase().includes(query)
    );
  }
  return filtered;
});

const filteredBySubject = computed(() => {
  let filtered = data.value.by_subject || [];
  if (selectedSubject.value) {
    filtered = filtered.filter(row => row.subject === selectedSubject.value);
  }
  return filtered;
});

const uniqueSubjects = computed(() => {
  return [...new Set((data.value.by_subject || []).map(s => s.subject))];
});

onMounted(load);
</script>

<template>
  <Head title="Teachers Reports" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Teachers Reports</h2>
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
              <div class="text-sm font-medium text-blue-600">Total Teachers</div>
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
              <div class="text-sm font-medium text-green-600">Male Teachers</div>
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
              <div class="text-sm font-medium text-pink-600">Female Teachers</div>
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

      <!-- Teachers by Subject -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Teachers by Subject</h3>
            <select v-model="selectedSubject" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
              <option value="">All Subjects</option>
              <option v-for="subject in uniqueSubjects" :key="subject" :value="subject">{{ subject }}</option>
            </select>
          </div>
        </div>
        <div class="p-6">
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="item in filteredBySubject" :key="item.subject" class="rounded-lg border border-gray-200 bg-gradient-to-br from-gray-50 to-white p-4 hover:shadow-md transition-shadow">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="font-medium text-gray-900">{{ item.subject }}</div>
                  <div class="mt-1 text-sm text-gray-500">Subject</div>
                </div>
                <div class="text-right">
                  <div class="text-2xl font-bold text-blue-600">{{ item.count }}</div>
                  <div class="text-xs text-gray-500">Teachers</div>
                </div>
              </div>
            </div>
            <div v-if="filteredBySubject.length === 0" class="col-span-full py-8 text-center text-sm text-gray-500">
              No subjects found
            </div>
          </div>
        </div>
      </div>

      <!-- Teachers by Class -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Class Teachers Assignment</h3>
            <input
              v-model="searchQuery"
              type="search"
              placeholder="Search teacher or class..."
              class="rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500 sm:w-64"
            />
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                  Class
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                  Teacher Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                  Phone
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="(row, idx) in filteredByClass" :key="idx" class="hover:bg-gray-50 transition-colors">
                <td class="whitespace-nowrap px-6 py-4">
                  <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
                    {{ row.class_name || '—' }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                  {{ row.teacher_name || '—' }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                  {{ row.phone || '—' }}
                </td>
              </tr>
              <tr v-if="filteredByClass.length === 0">
                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                  No teachers found
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

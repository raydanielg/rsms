<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  klass: { type: Object, required: true },
  students: { type: Array, required: true },
});
</script>

<template>
  <Head :title="`Class: ${$props.klass.name}`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Class: {{ $props.klass.name }}</h2>
        <Link :href="route('classes.index')" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Back</Link>
      </div>
    </template>

    <div class="space-y-4">
      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="mb-2 text-sm font-semibold text-gray-700">Assigned subjects</div>
        <div class="mb-3 flex flex-wrap gap-2">
          <span v-for="s in $props.klass.subjects" :key="s.id" :title="s.name" class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ s.code || s.name }}</span>
          <span v-if="!$props.klass.subjects || !$props.klass.subjects.length" class="text-xs text-gray-500">No subjects</span>
        </div>
        <div class="overflow-hidden rounded-md border border-gray-100" v-if="$props.klass.subjects && $props.klass.subjects.length">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Code</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Name</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="s in $props.klass.subjects" :key="s.id" class="hover:bg-gray-50">
                <td class="px-4 py-2 text-gray-900">{{ s.code || '—' }}</td>
                <td class="px-4 py-2 text-gray-700">{{ s.name }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Reg_No</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Full name</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Sex</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="s in students" :key="s.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-900">{{ s.reg_no }}</td>
                <td class="px-4 py-3 text-gray-700">{{ s.name }}</td>
                <td class="px-4 py-3"><span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ s.sex }}</span></td>
              </tr>
              <tr v-if="students.length===0">
                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">No students in this class.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

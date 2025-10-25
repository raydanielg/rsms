<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  subject: { type: Object, required: true },
  class_stats: { type: Array, required: true },
  total_students: { type: Number, required: true },
});
</script>

<template>
  <Head :title="`${subject.name} (${subject.code})`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ subject.name }} <span class="text-sm text-gray-500">({{ subject.code }})</span></h2>
          <div class="mt-1 text-xs text-gray-500">Total students in assigned classes: <span class="font-semibold text-gray-700">{{ total_students }}</span></div>
        </div>
        <Link :href="route('subjects.index')" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Back</Link>
      </div>
    </template>

    <div class="space-y-4">
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-700">Assigned Classes</div>
        <div class="p-4">
          <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="c in class_stats" :key="c.id" class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm">
              <div class="flex items-center justify-between">
                <div class="text-sm font-semibold text-gray-800">{{ c.name }}</div>
                <div class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-700">{{ c.students }} students</div>
              </div>
              <div class="mt-3 h-2 rounded-full bg-gray-100">
                <div class="h-2 rounded-full bg-green-500" :style="{ width: (total_students ? Math.round((c.students/total_students)*100) : 0) + '%' }"></div>
              </div>
            </div>
            <div v-if="class_stats.length===0" class="text-sm text-gray-500">No classes assigned to this subject.</div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

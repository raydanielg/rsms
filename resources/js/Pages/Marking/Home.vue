<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  exams: { type: Array, default: () => [] },
});

const colors = [
  'from-emerald-500 to-green-600',
  'from-indigo-500 to-blue-600',
  'from-amber-500 to-orange-600',
  'from-rose-500 to-red-600',
  'from-sky-500 to-cyan-600',
  'from-fuchsia-500 to-purple-600',
];
</script>

<template>
  <Head title="Marking" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Marking</h2>
      </div>
    </template>

    <div class="space-y-4">
      <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <p class="mb-4 text-sm text-gray-700">
          Chagua mtihani kuanza kuweka alama. Unaweza pia kuunda/kuhariri mitihani kupitia Results.
        </p>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <Link
            v-for="(e, idx) in exams"
            :key="e.id"
            :href="route('marks.show', { examId: e.id })"
            class="group relative overflow-hidden rounded-xl border border-black/5 bg-gradient-to-r p-5 text-white shadow hover:shadow-md"
            :class="colors[idx % colors.length]"
          >
            <div class="flex items-start justify-between">
              <div>
                <div class="text-sm/5 uppercase tracking-wide opacity-90">{{ e.type }}</div>
                <div class="mt-1 text-lg font-semibold">{{ e.name }}</div>
                <div class="mt-1 text-xs/5 opacity-90">Year {{ e.year }} · Term {{ e.term }}</div>
              </div>
              <svg class="h-6 w-6 opacity-80 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </div>
            <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10"></div>
          </Link>

          <div v-if="!exams || exams.length === 0" class="col-span-full rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-600">
            Hakuna mitihani. <Link :href="route('results.index')" class="text-green-700 underline">Nenda Results</Link> kuunda mpya.
          </div>
        </div>

        <div class="mt-5">
          <Link :href="route('results.index')" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
            Go to Results
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

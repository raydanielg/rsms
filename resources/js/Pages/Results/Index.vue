<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage, Link } from '@inertiajs/vue3';

const props = defineProps({
  exams: { type: Object, required: true },
  filters: { type: Object, default: () => ({}) },
  classes: { type: Array, required: true },
  types: { type: Array, required: true },
  current_year: { type: Number, required: true },
});

// Filters
const q = ref(props.filters.q || '');
const fType = ref(props.filters.type || '');
const fYear = ref(props.filters.year || '');
const fTerm = ref(props.filters.term || '');
function submitFilters(pageUrl = route('results.index')) {
  router.get(pageUrl, { q: q.value, type: fType.value, year: fYear.value, term: fTerm.value }, { preserveScroll: true, preserveState: true, replace: true });
}

// Create Exam modal
const showCreate = ref(false);
const exam = ref({ name: '', type: 'midterm', year: props.current_year, term: 1, class_ids: [] });
const creating = ref(false);
const clsFilter = ref('');
const filteredClasses = computed(() => {
  const term = clsFilter.value.trim().toLowerCase();
  if (!term) return props.classes;
  return props.classes.filter(c => (c.name || '').toLowerCase().includes(term));
});
function openCreate() {
  exam.value = { name: '', type: 'midterm', year: props.current_year, term: 1, class_ids: [] };
  clsFilter.value = '';
  showCreate.value = true;
}
async function submitCreate() {
  creating.value = true;
  try {
    await router.post(route('results.exams.store'), exam.value, {
      preserveScroll: true,
      onSuccess: () => { showCreate.value = false; window.__toast && window.__toast('success','Exam created'); },
      onError: (e) => { const m = Object.values(e||{})[0] || 'Failed to create'; window.__toast && window.__toast('error', String(m)); }
    });
  } finally { creating.value = false; }
}

const page = usePage();
const errors = computed(() => page.props.errors || {});

// Edit Exam modal
const showEdit = ref(false);
const editLoading = ref(false);
const edit = ref({ id: null, name: '', type: 'midterm', year: props.current_year, term: 1, class_ids: [] });
async function openEditExam(row) {
  showEdit.value = true; editLoading.value = true;
  try {
    const id = row.id;
    const res = await fetch(route('results.exams.show.json', { id }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const ex = res.ok ? await res.json() : null;
    if (!ex) { window.__toast && window.__toast('error','Failed to load exam'); return; }
    edit.value = { id: ex.id, name: ex.name, type: ex.type, year: ex.year, term: ex.term, class_ids: ex.class_ids || [] };
  } finally { editLoading.value = false; }
}
const saving = ref(false);
async function submitEdit() {
  if (!edit.value.id) return;
  saving.value = true;
  try {
    await router.patch(route('results.exams.update', { id: edit.value.id }), { name: edit.value.name, type: edit.value.type, year: edit.value.year, term: edit.value.term, class_ids: edit.value.class_ids }, {
      preserveScroll: true,
      onSuccess: () => { showEdit.value = false; window.__toast && window.__toast('success','Exam updated'); },
      onError: (e) => { const m = Object.values(e||{})[0] || 'Failed to update'; window.__toast && window.__toast('error', String(m)); }
    });
  } finally { saving.value = false; }
}

// Delete Exam
const showDelete = ref(false);
const del = ref({ id: null, name: '' });
const deleting = ref(false);
function openDeleteExam(row) { del.value = { id: row.id, name: row.name }; showDelete.value = true; }
async function confirmDeleteExam() {
  if (!del.value.id) return;
  deleting.value = true;
  try {
    await router.delete(route('results.exams.destroy', { id: del.value.id }), {
      preserveScroll: true,
      onSuccess: () => { showDelete.value = false; window.__toast && window.__toast('success','Exam deleted'); },
      onError: () => { window.__toast && window.__toast('error','Failed to delete'); }
    });
  } finally { deleting.value = false; }
}
</script>

<template>
  <Head title="Results" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Results</h2>
        <button type="button" @click="openCreate" class="rounded-md bg-gradient-to-r from-green-600 to-emerald-600 px-3 py-2 text-sm font-medium text-white shadow hover:from-green-700 hover:to-emerald-700">Create Exam</button>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Filters -->
      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="grid gap-3 sm:grid-cols-5">
          <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-medium text-gray-600">Search exam</label>
            <input v-model="q" @keyup.enter="submitFilters()" type="text" placeholder="e.g. Midterm" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Type</label>
            <select v-model="fType" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option value="">All</option>
              <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Year</label>
            <input v-model="fYear" type="number" min="2000" max="2100" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Term</label>
            <select v-model="fTerm" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option value="">All</option>
              <option :value="1">1</option>
              <option :value="2">2</option>
              <option :value="3">3</option>
            </select>
          </div>
        </div>
        <div class="mt-3 flex justify-end gap-2">
          <button type="button" @click="() => { q=''; fType=''; fYear=''; fTerm=''; submitFilters(); }" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Reset</button>
          <button type="button" @click="submitFilters()" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">Search</button>
        </div>
      </div>

      <!-- Exams Table -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Name</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Type</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Year/Term</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Classes</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-700">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="e in exams.data" :key="e.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-900">{{ e.name }}</td>
                <td class="px-4 py-3 text-gray-700">{{ e.type }}</td>
                <td class="px-4 py-3 text-gray-700">{{ e.year }}/T{{ e.term }}</td>
                <td class="px-4 py-3 text-gray-700">
                  <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ e.classes_count }} classes</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <!-- Marking (forward arrow) -->
                    <Link :href="route('marking.show', { examId: e.id })" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-green-50 text-green-600 ring-1 ring-inset ring-green-200 hover:bg-green-100" aria-label="Mark">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                    <!-- View -->
                    <Link :href="route('results.exams.show', { id: e.id })" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-100" aria-label="View">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/></svg>
                    </Link>
                    <!-- Edit -->
                    <button type="button" @click="openEditExam(e)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100" aria-label="Edit">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 113 3L8.25 18.1 4.5 19.5l1.4-3.75 10.962-12.263z"/></svg>
                    </button>
                    <!-- Delete -->
                    <button type="button" @click="openDeleteExam(e)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200 hover:bg-rose-100" aria-label="Delete">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-1 0l-.867 12.142A2 2 0 0114.138 21H9.862a2 2 0 01-1.995-1.858L7 7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="(exams.data || []).length === 0">
                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No exams found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="exams.links" class="flex flex-wrap items-center justify-between gap-2">
        <div class="text-xs text-gray-500">Showing {{ exams.from || 0 }}-{{ exams.to || 0 }} of {{ exams.total || 0 }}</div>
        <div class="flex flex-wrap gap-1">
          <button v-for="(link, i) in exams.links" :key="i" :disabled="!link.url" @click="link.url && submitFilters(link.url)" class="rounded-md px-3 py-1.5 text-sm" :class="[ link.active ? 'bg-green-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50', !link.url ? 'opacity-50' : '' ]" v-html="link.label" />
        </div>
      </div>
    </div>

    <!-- Create Exam Modal -->
    <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="showCreate = false"></div>
      <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <h3 class="text-base font-semibold text-gray-800">Create Exam</h3>
          <button @click="showCreate = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="grid gap-4 p-5 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Exam name <span class="text-red-600">*</span></label>
            <input v-model="exam.name" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
            <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Type <span class="text-red-600">*</span></label>
            <select v-model="exam.type" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Year <span class="text-red-600">*</span></label>
            <input v-model="exam.year" type="number" min="2000" max="2100" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Term <span class="text-red-600">*</span></label>
            <select v-model="exam.term" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option :value="1">1</option>
              <option :value="2">2</option>
              <option :value="3">3</option>
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-medium text-gray-600">Assign classes</label>
            <div class="mb-2 flex items-end justify-between gap-2">
              <div class="flex-1">
                <input v-model="clsFilter" type="text" placeholder="Filter classes..." class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              </div>
              <div class="flex gap-2">
                <button type="button" @click="exam.class_ids = filteredClasses.map(c=>c.id)" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50">Select all</button>
                <button type="button" @click="exam.class_ids = []" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50">Clear</button>
              </div>
            </div>
            <div class="flex max-h-60 flex-wrap gap-2 overflow-auto rounded-md border border-gray-200 p-2">
              <label v-for="c in filteredClasses" :key="c.id" class="flex items-center gap-2 rounded-md bg-gray-50 px-2 py-1 text-sm text-gray-700 ring-1 ring-inset ring-gray-200">
                <input type="checkbox" :value="c.id" v-model="exam.class_ids" /> {{ c.name }}
              </label>
              <div v-if="filteredClasses.length===0" class="px-2 py-1 text-xs text-gray-500">No classes found.</div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t px-5 py-3">
          <button @click="showCreate = false" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
          <button :disabled="creating" @click="submitCreate" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ creating ? 'Creating...' : 'Create Exam' }}</button>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>

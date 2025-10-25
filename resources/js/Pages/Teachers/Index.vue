<script setup>
import { ref, computed, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
  teachers: { type: Object, required: true },
  filters: { type: Object, default: () => ({ q: '', from: '', to: '' }) },
});

const q = ref(props.filters.q || '');
const from = ref(props.filters.from || '');
const to = ref(props.filters.to || '');
function applyFilters() {
  router.get(
    route('teachers.index'),
    { q: q.value, from: from.value, to: to.value },
    { preserveState: true, replace: true }
  );
}
function exportPdf() {
  const params = new URLSearchParams({ q: q.value || '', from: from.value || '', to: to.value || '', export: 'pdf' }).toString();
  const url = route('teachers.index') + (route('teachers.index').includes('?') ? '&' : '?') + params;
  window.open(url, '_blank');
}

// Add modal state
const showAdd = ref(false);
const saving = ref(false);
const add = ref({ name: '', check_no: '', phone: '', email: '', sex: 'M', classes: [], subjects: [] });
const canSave = computed(() => {
  return (
    add.value.name.trim() !== '' &&
    add.value.check_no.trim() !== '' &&
    add.value.phone.trim() !== '' &&
    (add.value.sex === 'M' || add.value.sex === 'F') &&
    add.value.classes.length > 0 &&
    add.value.subjects.length > 0
  );
});

// Data sources for selects
const classOpts = ref([]);
const classIds = ref([]); // will contain at most one id
const subjOpts = ref([]);
let classTimer, subjTimer;
async function fetchClasses() {
  // Load all/first page classes for the select (no typeahead)
  const res = await fetch(route('classes.search', { q: '' }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  classOpts.value = res.ok ? await res.json() : [];
}
async function fetchSubjects() {
  // Load subjects for the selected class id (if any)
  if (Array.isArray(classIds.value) && classIds.value.length > 0) {
    const id = classIds.value[0];
    const r = await fetch(route('classes.subjects', { id }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (r.ok) {
      const data = await r.json();
      const arr = Array.isArray(data) ? data : (Array.isArray(data?.subjects) ? data.subjects : []);
      // Deduplicate by id
      const map = new Map();
      for (const s of arr) { if (s && s.id != null) map.set(s.id, s); }
      subjOpts.value = Array.from(map.values());
    } else {
      subjOpts.value = [];
    }
  } else {
    subjOpts.value = [];
  }
  // Prune selected subjects that are no longer available
  const allowedIds = new Set(subjOpts.value.map(s => s.id));
  add.value.subjects = (add.value.subjects || []).filter(s => allowedIds.has(s.id));
}
function toggleSelect(listRef, item) {
  const idx = listRef.value.findIndex(x => x.id === item.id);
  if (idx === -1) listRef.value.push(item); else listRef.value.splice(idx, 1);
}
function removeFrom(listRef, id) {
  const idx = listRef.value.findIndex(x => x.id === id);
  if (idx !== -1) listRef.value.splice(idx, 1);
}
function syncAddClassesFromIds() {
  add.value.classes = classIds.value.slice(0,1).map(id => ({ id, name: (classOpts.value.find(c => c.id === id)?.name) || `#${id}` }));
}
function onClassesChange(e) {
  const id = Number(e.target.value || 0);
  classIds.value = isNaN(id) || id === 0 ? [] : [id];
  syncAddClassesFromIds();
  fetchSubjects();
}
function onSubjectsChange(e) {
  const ids = Array.from(e.target.selectedOptions).map(o => Number(o.value));
  const map = new Map(subjOpts.value.map(s => [s.id, s]));
  add.value.subjects = ids.map(id => map.get(id)).filter(Boolean);
}
async function saveTeacher() {
  saving.value = true;
  try {
    await router.post(route('teachers.store'), {
      name: add.value.name,
      check_no: add.value.check_no,
      phone: add.value.phone,
      email: add.value.email || null,
      sex: add.value.sex,
      class_ids: add.value.classes.map(c => c.id),
      subject_ids: add.value.subjects.map(s => s.id),
    }, { preserveState: false });
    showAdd.value = false;
    add.value = { name: '', check_no: '', phone: '', email: '', sex: 'M', classes: [], subjects: [] };
  } finally {
    saving.value = false;
  }
}

watch(showAdd, (v) => {
  if (v) {
    fetchClasses();
    classIds.value = [];
    add.value.classes = [];
    subjOpts.value = [];
    add.value.subjects = [];
  }
});

// Eye modal state
const showEye = ref(false);
const eyeLoading = ref(false);
const eye = ref(null);
async function openEye(t) {
  showEye.value = true;
  eyeLoading.value = true;
  try {
    const res = await fetch(route('teachers.show.json', { id: t.id }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    eye.value = res.ok ? await res.json() : t;
  } finally { eyeLoading.value = false; }
}

function subjLabel(s) { return s?.name || ''; }
</script>

<template>
  <Head title="Teachers" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Teachers</h2>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Toolbar: search + date filters + export + add -->
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-3 sm:gap-3">
          <input v-model="q" @keyup.enter="applyFilters" type="text" placeholder="Search name / check no / phone" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
          <input v-model="from" type="date" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
          <input v-model="to" type="date" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
        </div>
        <div class="flex shrink-0 items-center gap-2">
          <button @click="applyFilters" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Filter</button>
          <button @click="exportPdf" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-4-4m4 4l4-4M4 7v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3"/></svg>
            Export PDF
          </button>
          <button @click="showAdd = true" class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Add Teacher</button>
        </div>
      </div>

      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Name</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Check No</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Phone</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Classes</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Subjects</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="t in props.teachers.data" :key="t.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-900">{{ t.name }}</td>
                <td class="px-4 py-3 text-gray-700">{{ t.check_no }}</td>
                <td class="px-4 py-3 text-gray-700">{{ t.phone }}</td>
                <td class="px-4 py-3">
                  <div class="flex max-w-[240px] flex-wrap gap-1">
                    <span v-for="c in (t.classes || [])" :key="c.id" class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ c.name }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex max-w-[240px] flex-wrap gap-1">
                    <span v-for="s in (t.subjects || [])" :key="s.id" class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ subjLabel(s) }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <Link :href="route('teachers.show', { id: t.id })" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-green-50 text-green-600 ring-1 ring-inset ring-green-200 hover:bg-green-100" aria-label="Open overview">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                    <button type="button" @click="openEye(t)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-100" aria-label="View">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8zm11 3a3 3 0 110-6 3 3 0 010 6z"/></svg>
                    </button>
                    <Link as="button" method="delete" :href="route('teachers.destroy', { id: t.id })" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200 hover:bg-rose-100" aria-label="Delete" onclick="return confirm('Delete this teacher?')">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-1 0l-.867 12.142A2 2 0 0114.138 21H9.862a2 2 0 01-1.995-1.858L7 7z"/></svg>
                    </Link>
                  </div>
                </td>
              </tr>
              <tr v-if="props.teachers.data.length===0">
                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No teachers yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="flex items-center justify-between border-t bg-gray-50 px-4 py-2 text-sm text-gray-600">
          <div>Showing {{ props.teachers.from || 0 }}–{{ props.teachers.to || 0 }} of {{ props.teachers.total }}</div>
        </div>
      </div>
    </div>

    <!-- Add Teacher Modal -->
    <div v-if="showAdd" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="w-full max-w-2xl rounded-xl bg-white shadow-lg">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <div class="text-base font-semibold text-gray-800">Add Teacher</div>
          <button @click="showAdd = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="grid gap-4 p-5 sm:grid-cols-2">
          <div>
            <label class="text-xs text-gray-500">Full name <span class="text-rose-600">*</span></label>
            <input v-model="add.name" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
          </div>
          <div>
            <label class="text-xs text-gray-500">Check/Employee No <span class="text-rose-600">*</span></label>
            <input v-model="add.check_no" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
          </div>
          <div>
            <label class="text-xs text-gray-500">Phone <span class="text-rose-600">*</span></label>
            <input v-model="add.phone" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
          </div>
          <div>
            <label class="text-xs text-gray-500">Email</label>
            <input v-model="add.email" type="email" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
          </div>
          <div>
            <label class="text-xs text-gray-500">Sex <span class="text-rose-600">*</span></label>
            <select v-model="add.sex" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              <option value="M">Male</option>
              <option value="F">Female</option>
            </select>
          </div>
        </div>
        <div class="grid gap-4 p-5 pt-0 sm:grid-cols-2">
          <div>
            <label class="text-xs text-gray-500">Classes <span class="text-rose-600">*</span></label>
            <select class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" @change="onClassesChange">
              <option value="">Select class...</option>
              <option v-for="c in classOpts" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-gray-500">Subjects <span class="text-rose-600">*</span></label>
            <select multiple size="8" class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm focus:border-emerald-500 focus:ring-emerald-500" @change="onSubjectsChange">
              <option v-for="s in subjOpts" :key="s.id" :value="s.id">{{ subjLabel(s) }}</option>
            </select>
          </div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t px-5 py-3">
          <button @click="showAdd = false" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
          <button @click="saveTeacher" :disabled="saving || !canSave" class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-60">{{ saving ? 'Saving...' : 'Save' }}</button>
        </div>
      </div>
    </div>

    <!-- Eye Details Modal -->
    <div v-if="showEye" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="w-full max-w-3xl rounded-xl bg-white shadow-lg">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <div class="text-base font-semibold text-gray-800">Teacher Details</div>
          <button @click="showEye = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="p-5">
          <div v-if="eyeLoading" class="py-8 text-center text-sm text-gray-500">Loading...</div>
          <template v-else>
            <div class="grid gap-4 sm:grid-cols-3">
              <div>
                <div class="text-xs text-gray-500">Full name</div>
                <div class="mt-1 text-base font-semibold text-gray-900">{{ eye?.name }}</div>
                <div class="text-xs text-gray-500">Check No</div>
                <div class="mt-1 text-sm text-gray-800">{{ eye?.check_no }}</div>
              </div>
              <div>
                <div class="text-xs text-gray-500">Phone</div>
                <div class="mt-1 text-sm text-gray-800">{{ eye?.phone }}</div>
                <div class="text-xs text-gray-500">Email</div>
                <div class="mt-1 text-sm text-gray-800">{{ eye?.email || '—' }}</div>
              </div>
              <div>
                <div class="text-xs text-gray-500">Sex</div>
                <div class="mt-1 inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ eye?.sex }}</div>
              </div>
            </div>
            <div class="mt-4">
              <div class="text-sm font-semibold text-gray-800">Assigned classes</div>
              <div class="mt-1 flex flex-wrap gap-1">
                <span v-for="c in (eye?.classes || [])" :key="c.id" class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ c.name }}</span>
                <span v-if="!eye?.classes || eye.classes.length===0" class="text-xs text-gray-500">No classes</span>
              </div>
              <div v-if="eye?.classes && eye.classes.length" class="mt-2 overflow-hidden rounded-md border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm"><thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left font-semibold text-gray-700">Class</th></tr></thead><tbody class="divide-y divide-gray-100"><tr v-for="c in eye.classes" :key="c.id"><td class="px-3 py-2 text-gray-800">{{ c.name }}</td></tr></tbody></table>
              </div>
            </div>
            <div class="mt-4">
              <div class="text-sm font-semibold text-gray-800">Assigned subjects</div>
              <div class="mt-1 flex flex-wrap gap-1">
                <span v-for="s in (eye?.subjects || [])" :key="s.id" class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ subjLabel(s) }}</span>
                <span v-if="!eye?.subjects || eye.subjects.length===0" class="text-xs text-gray-500">No subjects</span>
              </div>
              <div v-if="eye?.subjects && eye.subjects.length" class="mt-2 overflow-hidden rounded-md border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm"><thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left font-semibold text-gray-700">Code</th><th class="px-3 py-2 text-left font-semibold text-gray-700">Name</th></tr></thead><tbody class="divide-y divide-gray-100"><tr v-for="s in eye.subjects" :key="s.id"><td class="px-3 py-2 text-gray-900">{{ s.code || '—' }}</td><td class="px-3 py-2 text-gray-700">{{ s.name }}</td></tr></tbody></table>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

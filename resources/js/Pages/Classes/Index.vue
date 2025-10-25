<script setup>
import { ref, computed, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage, Link } from '@inertiajs/vue3';

const props = defineProps({
  classes: { type: Object, required: true },
  subjects: { type: Array, required: true },
  filters: { type: Object, default: () => ({ q: '' }) },
});

// Make short subject label (prefer code; otherwise initials of name)
function subjLabel(s) {
  if (!s) return '—';
  if (s.code && String(s.code).trim()) return String(s.code).trim();
  if (s.name && String(s.name).trim()) {
    const initials = String(s.name).trim().split(/\s+/).map(w => w[0] || '').join('').toUpperCase();
    return initials.slice(0, 4);
  }
  return '—';
}

// Search and suggestions
const q = ref(props.filters.q || '');
function submitFilters(pageUrl = route('classes.index')) {
  router.get(pageUrl, { q: q.value }, { preserveScroll: true, preserveState: true, replace: true });
}
const sug = ref({ open: false, items: [], loading: false, index: -1 });
let qDebounce;
watch(q, (val) => {
  if (qDebounce) clearTimeout(qDebounce);
  qDebounce = setTimeout(async () => {
    submitFilters();
    if (!val || val.length < 2) { sug.value = { open: false, items: [], loading: false, index: -1 }; return; }
    sug.value.loading = true;
    try {
      const res = await fetch(route('classes.search', { q: val }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      sug.value.items = res.ok ? await res.json() : [];
      sug.value.open = sug.value.items.length > 0;
    } finally { sug.value.loading = false; }
  }, 300);
});
function onSearchKeydown(e) {
  if (!sug.value.open) return;
  if (e.key === 'ArrowDown') { e.preventDefault(); sug.value.index = Math.min(sug.value.index + 1, sug.value.items.length - 1); }
  if (e.key === 'ArrowUp') { e.preventDefault(); sug.value.index = Math.max(sug.value.index - 1, 0); }
  if (e.key === 'Enter' && sug.value.index >= 0) {
    e.preventDefault();
    const item = sug.value.items[sug.value.index];
    if (item) openEdit(item);
  }
  if (e.key === 'Escape') sug.value.open = false;
}

// Toasts and errors
const page = usePage();
const errors = computed(() => page.props.errors || {});

// Add class modal (wizard)
const showAdd = ref(false);
const addStep = ref(1);
const add = ref({ name: '', subject_ids: [] });
const adding = ref(false);
const subjFilter = ref('');
const filteredSubjects = computed(() => {
  const q = subjFilter.value.trim().toLowerCase();
  if (!q) return props.subjects;
  return props.subjects.filter(s => (s.name + ' ' + (s.code || '')).toLowerCase().includes(q));
});
function openAdd() {
  add.value = { name: '', subject_ids: [] };
  addStep.value = 1;
  subjFilter.value = '';
  showAdd.value = true;
}
function nextAddStep() { if (addStep.value < 2) addStep.value++; }
function prevAddStep() { if (addStep.value > 1) addStep.value--; }
function selectAllSubjects() { add.value.subject_ids = filteredSubjects.value.map(s => s.id); }
function clearAllSubjects() { add.value.subject_ids = []; }
async function submitAdd() {
  adding.value = true;
  try {
    await router.post(route('classes.store'), add.value, {
      preserveScroll: true,
      onSuccess: () => { showAdd.value = false; add.value = { name: '', subject_ids: [] }; window.__toast && window.__toast('success','Class created'); },
      onError: (e) => { const m = Object.values(e||{})[0] || 'Failed to create'; window.__toast && window.__toast('error', String(m)); }
    });
  } finally { adding.value = false; }
}

// Edit class modal
const showEdit = ref(false);
const editLoading = ref(false);
const edit = ref({ id: null, name: '', subject_ids: [] });
async function openEdit(item) {
  showEdit.value = true; editLoading.value = true;
  try {
    const id = item.id;
    const res = await fetch(route('classes.show.json', { id }), { headers: { 'X-Requested-With':'XMLHttpRequest' } });
    const cls = res.ok ? await res.json() : null;
    if (!cls) { window.__toast && window.__toast('error','Failed to load class'); return; }
    edit.value = { id: cls.id, name: cls.name, subject_ids: (cls.subjects||[]).map(s=>s.id) };
  } finally { editLoading.value = false; }
}
const editing = ref(false);
async function submitEdit() {
  if (!edit.value.id) return;
  editing.value = true;
  try {
    await router.patch(route('classes.update', { id: edit.value.id }), { name: edit.value.name, subject_ids: edit.value.subject_ids }, {
      preserveScroll: true,
      onSuccess: () => { showEdit.value = false; window.__toast && window.__toast('success','Class updated'); },
      onError: (e) => { const m = Object.values(e||{})[0] || 'Failed to update'; window.__toast && window.__toast('error', String(m)); }
    });
  } finally { editing.value = false; }
}

// Delete confirm
const showDelete = ref(false);
const del = ref({ id: null, name: '' });
const deleting = ref(false);
function openDelete(item) { del.value = { id: item.id, name: item.name }; showDelete.value = true; }
async function confirmDelete() {
  if (!del.value.id) return;
  deleting.value = true;
  try {
    await router.delete(route('classes.destroy', { id: del.value.id }), {
      preserveScroll: true,
      onSuccess: () => { showDelete.value = false; window.__toast && window.__toast('success','Class deleted'); },
      onError: () => { window.__toast && window.__toast('error','Failed to delete'); }
    });
  } finally { deleting.value = false; }
}
</script>

<template>
  <Head title="Classes" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">All Classes</h2>
    </template>

    <div class="space-y-4">
      <!-- Search -->
      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="flex items-end justify-between gap-3">
          <div class="relative max-w-md flex-1">
            <label for="q" class="mb-1 block text-xs font-medium text-gray-600">Search class</label>
            <input id="q" v-model="q" @keydown="onSearchKeydown" type="text" placeholder="e.g. Form 1" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
            <div v-if="sug.open" class="absolute z-20 mt-1 w-full overflow-hidden rounded-md border border-gray-200 bg-white shadow">
            <div v-if="sug.loading" class="p-2 text-xs text-gray-500">Loading...</div>
            <ul v-else class="max-h-60 overflow-auto text-sm">
              <li v-for="(it, i) in sug.items" :key="it.id" @click="openEdit(it)" :class="['cursor-pointer px-3 py-2 hover:bg-gray-50', i===sug.index ? 'bg-gray-50' : '']">
                <div class="flex items-center justify-between">
                  <div class="font-medium text-gray-800">{{ it.name }}</div>
                </div>
              </li>
            </ul>
            </div>
          </div>
          <div>
            <button type="button" @click="openAdd" class="rounded-md bg-gradient-to-r from-green-600 to-emerald-600 px-3 py-2 text-sm font-medium text-white shadow hover:from-green-700 hover:to-emerald-700">Add Class</button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Class name</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700" title="Subject codes">Assigned subjects (codes)</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-700">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="c in classes.data" :key="c.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-900">
                  <Link :href="route('classes.show', { id: c.id })" class="text-green-700 hover:underline">{{ c.name }}</Link>
                </td>
                <td class="px-4 py-3 text-gray-700" :title="(c.subjects||[]).map(s => (s.code || s.name)).join(', ')">
                  <div class="flex flex-wrap items-center gap-1">
                    <template v-if="c.subjects && c.subjects.length">
                      <span v-for="s in c.subjects.slice(0, 6)" :key="s.id" :title="s.name + (s.code ? ' • '+s.code : '')"
                            class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                        {{ subjLabel(s) }}
                      </span>
                      <span v-if="c.subjects.length > 6" class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">+{{ c.subjects.length - 6 }} more</span>
                    </template>
                    <span v-else class="text-xs text-gray-500">No subjects</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <Link :href="route('classes.show', { id: c.id })" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-green-50 text-green-600 ring-1 ring-inset ring-green-200 hover:bg-green-100" aria-label="Open overview">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                    <button type="button" @click.stop="openEdit(c)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100" aria-label="Edit">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 113 3L8.25 18.1 4.5 19.5l1.4-3.75 10.962-12.263z"/></svg>
                    </button>
                    <button type="button" @click.stop="openDelete(c)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200 hover:bg-rose-100" aria-label="Delete">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-1 0l-.867 12.142A2 2 0 0114.138 21H9.862a2 2 0 01-1.995-1.858L7 7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="classes.links" class="flex flex-wrap items-center justify-between gap-2">
        <div class="text-xs text-gray-500">Showing {{ classes.from || 0 }}-{{ classes.to || 0 }} of {{ classes.total || 0 }}</div>
        <div class="flex flex-wrap gap-1">
          <button v-for="(link, i) in classes.links" :key="i" :disabled="!link.url" @click="link.url && submitFilters(link.url)" class="rounded-md px-3 py-1.5 text-sm" :class="[ link.active ? 'bg-green-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50', !link.url ? 'opacity-50' : '' ]" v-html="link.label" />
        </div>
      </div>
    </div>

    <!-- Add Modal (Wizard) -->
    <div v-if="showAdd" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="showAdd = false"></div>
      <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl">
        <!-- Colorful header with steps -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-5 py-4 text-white">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold">Add Class</h3>
            <button @click="showAdd = false" class="rounded-md p-1 hover:bg-white/10" aria-label="Close">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div class="mt-3 flex items-center gap-2 text-xs">
            <div :class="['flex-1 rounded-full px-3 py-1', addStep===1 ? 'bg-white/90 text-green-700' : 'bg-white/30 text-white']">Step 1: Basic info</div>
            <div :class="['flex-1 rounded-full px-3 py-1', addStep===2 ? 'bg-white/90 text-green-700' : 'bg-white/30 text-white']">Step 2: Assign subjects</div>
          </div>
        </div>
        <div class="space-y-4 p-5">
          <!-- Step 1 -->
          <div v-if="addStep === 1" class="space-y-4">
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Class name <span class="text-red-600">*</span></label>
              <input v-model="add.name" required type="text" placeholder="e.g. Form 1A" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Class ID (auto)</label>
              <div class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs text-gray-700">
                <svg class="h-3.5 w-3.5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10v10H7z"/></svg>
                <span>{{ add.name ? (add.name.toUpperCase().replace(/\s+/g,'-').slice(0,15)) : 'Will be generated automatically' }}</span>
              </div>
            </div>
          </div>

          <!-- Step 2 -->
          <div v-else class="space-y-3">
            <div class="flex items-end justify-between gap-3">
              <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-600">Filter subjects</label>
                <input v-model="subjFilter" type="text" placeholder="Search subject..." class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              </div>
              <div class="flex gap-2">
                <button type="button" @click="selectAllSubjects" class="rounded-md border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">Select all</button>
                <button type="button" @click="clearAllSubjects" class="rounded-md border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">Clear</button>
              </div>
            </div>
            <div class="flex max-h-60 flex-wrap gap-2 overflow-auto rounded-md border border-gray-200 p-2">
              <label v-for="s in filteredSubjects" :key="s.id" class="flex items-center gap-2 rounded-md bg-gray-50 px-2 py-1 text-sm text-gray-700 ring-1 ring-inset ring-gray-200">
                <input type="checkbox" :value="s.id" v-model="add.subject_ids" /> {{ s.name }}
              </label>
              <div v-if="filteredSubjects.length===0" class="px-2 py-1 text-xs text-gray-500">No subjects found.</div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-between gap-2 border-t px-5 py-3">
          <div>
            <button v-if="addStep>1" @click="prevAddStep" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Back</button>
          </div>
          <div class="ml-auto flex items-center gap-2">
            <button @click="showAdd = false" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
            <button v-if="addStep===1" :disabled="!add.name" @click="nextAddStep" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">Next</button>
            <button v-else :disabled="adding" @click="submitAdd" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ adding ? 'Saving...' : 'Save' }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showEdit" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="showEdit = false"></div>
      <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <h3 class="text-base font-semibold text-gray-800">Edit Class</h3>
          <button @click="showEdit = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="space-y-4 p-5">
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Class name <span class="text-red-600">*</span></label>
            <input v-model="edit.name" required type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Assign subjects</label>
            <div class="flex max-h-48 flex-wrap gap-2 overflow-auto rounded-md border border-gray-200 p-2">
              <label v-for="s in subjects" :key="s.id" class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" :value="s.id" v-model="edit.subject_ids" /> {{ s.name }}
              </label>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t px-5 py-3">
          <button @click="showEdit = false" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
          <button :disabled="editing" @click="submitEdit" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ editing ? 'Saving...' : 'Save changes' }}</button>
        </div>
      </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div v-if="showDelete" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="showDelete = false"></div>
      <div class="relative z-10 w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <h3 class="text-base font-semibold text-gray-800">Delete Class</h3>
          <button @click="showDelete = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="space-y-3 p-5 text-sm">
          <p class="text-gray-700">Are you sure you want to delete <span class="font-semibold">{{ del.name }}</span>?</p>
        </div>
        <div class="flex items-center justify-end gap-2 border-t px-5 py-3">
          <button @click="showDelete = false" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
          <button :disabled="deleting" @click="confirmDelete" type="button" class="rounded-md bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 disabled:opacity-60">{{ deleting ? 'Deleting...' : 'Delete' }}</button>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>

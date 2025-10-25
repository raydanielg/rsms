<script setup>
import { ref, computed, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage, Link } from '@inertiajs/vue3';

const props = defineProps({
  subjects: { type: Object, required: true },
  classes: { type: Array, required: true },
  filters: { type: Object, default: () => ({ q: '' }) },
});

// Search and suggestions
const q = ref(props.filters.q || '');
function submitFilters(pageUrl = route('subjects.index')) {
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
      const res = await fetch(route('subjects.search', { q: val }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
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

// Add subject modal
const showAdd = ref(false);
const add = ref({ name: '', code: '', class_ids: [] });
const adding = ref(false);
const clsFilter = ref('');
const filteredClasses = computed(() => {
  const term = clsFilter.value.trim().toLowerCase();
  if (!term) return props.classes;
  return props.classes.filter(c => (c.name || '').toLowerCase().includes(term));
});
function openAdd() {
  add.value = { name: '', code: '', class_ids: [] };
  clsFilter.value = '';
  showAdd.value = true;
}
async function submitAdd() {
  adding.value = true;
  try {
    await router.post(route('subjects.store'), add.value, {
      preserveScroll: true,
      onSuccess: () => { showAdd.value = false; window.__toast && window.__toast('success','Subject created'); },
      onError: (e) => { const m = Object.values(e||{})[0] || 'Failed to create'; window.__toast && window.__toast('error', String(m)); }
    });
  } finally { adding.value = false; }
}

// Edit subject modal
const showEdit = ref(false);
const editLoading = ref(false);
const edit = ref({ id: null, name: '', code: '', class_ids: [] });
async function openEdit(item) {
  showEdit.value = true; editLoading.value = true;
  try {
    const id = item.id;
    const res = await fetch(route('subjects.show.json', { id }), { headers: { 'X-Requested-With':'XMLHttpRequest' } });
    const s = res.ok ? await res.json() : null;
    if (!s) { window.__toast && window.__toast('error','Failed to load subject'); return; }
    edit.value = { id: s.id, name: s.name, code: s.code, class_ids: (s.classes||[]).map(c=>c.id) };
  } finally { editLoading.value = false; }
}
const editing = ref(false);
async function submitEdit() {
  if (!edit.value.id) return;
  editing.value = true;
  try {
    await router.patch(route('subjects.update', { id: edit.value.id }), { name: edit.value.name, code: edit.value.code, class_ids: edit.value.class_ids }, {
      preserveScroll: true,
      onSuccess: () => { showEdit.value = false; window.__toast && window.__toast('success','Subject updated'); },
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
    await router.delete(route('subjects.destroy', { id: del.value.id }), {
      preserveScroll: true,
      onSuccess: () => { showDelete.value = false; window.__toast && window.__toast('success','Subject deleted'); },
      onError: () => { window.__toast && window.__toast('error','Failed to delete'); }
    });
  } finally { deleting.value = false; }
}
</script>

<template>
  <Head title="Subjects" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Subjects</h2>
    </template>

    <div class="space-y-4">
      <!-- Search + Add -->
      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="flex items-end justify-between gap-3">
          <div class="relative max-w-md flex-1">
            <label for="q" class="mb-1 block text-xs font-medium text-gray-600">Search subject</label>
            <input id="q" v-model="q" @keydown="onSearchKeydown" type="text" placeholder="e.g. Mathematics or MATH" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
            <div v-if="sug.open" class="absolute z-20 mt-1 w-full overflow-hidden rounded-md border border-gray-200 bg-white shadow">
              <div v-if="sug.loading" class="p-2 text-xs text-gray-500">Loading...</div>
              <ul v-else class="max-h-60 overflow-auto text-sm">
                <li v-for="(it, i) in sug.items" :key="it.id" @click="openEdit(it)" :class="['cursor-pointer px-3 py-2 hover:bg-gray-50', i===sug.index ? 'bg-gray-50' : '']">
                  <div class="flex items-center justify-between">
                    <div class="font-medium text-gray-800">{{ it.name }}</div>
                    <div class="text-xs text-gray-500">{{ it.code }}</div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div>
            <button type="button" @click="openAdd" class="rounded-md bg-gradient-to-r from-green-600 to-emerald-600 px-3 py-2 text-sm font-medium text-white shadow hover:from-green-700 hover:to-emerald-700">Add Subject</button>
          </div>
        </div>
      </div>

      <!-- Scope info -->
      <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs text-emerald-800">
        Subjects listed here are scoped to your school. Codes must be unique within your school.
      </div>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Name</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Code</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Assigned classes</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Count</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-700">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="s in subjects.data" :key="s.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-900">
                  <Link :href="route('subjects.show', { id: s.id })" class="text-green-700 hover:underline">{{ s.name }}</Link>
                </td>
                <td class="px-4 py-3"><span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ s.code }}</span></td>
                <td class="px-4 py-3 text-gray-700">
                  <div class="flex flex-wrap gap-1">
                    <span v-for="c in s.classes" :key="c.id" class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ c.name }}</span>
                    <span v-if="!s.classes || s.classes.length===0" class="text-xs text-gray-500">No classes</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700 ring-1 ring-inset ring-emerald-200">{{ (s.classes && s.classes.length) || 0 }}</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <button type="button" @click.stop="openEdit(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100" aria-label="Edit">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 113 3L8.25 18.1 4.5 19.5l1.4-3.75 10.962-12.263z"/></svg>
                    </button>
                    <button type="button" @click.stop="openDelete(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200 hover:bg-rose-100" aria-label="Delete">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-1 0l-.867 12.142A2 2 0 0114.138 21H9.862a2 2 0 01-1.995-1.858L7 7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="(subjects.data || []).length === 0">
                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No subjects found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="subjects.links" class="flex flex-wrap items-center justify-between gap-2">
        <div class="text-xs text-gray-500">Showing {{ subjects.from || 0 }}-{{ subjects.to || 0 }} of {{ subjects.total || 0 }}</div>
        <div class="flex flex-wrap gap-1">
          <button v-for="(link, i) in subjects.links" :key="i" :disabled="!link.url" @click="link.url && submitFilters(link.url)" class="rounded-md px-3 py-1.5 text-sm" :class="[ link.active ? 'bg-green-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50', !link.url ? 'opacity-50' : '' ]" v-html="link.label" />
        </div>
      </div>
    </div>

    <!-- Add Modal -->
    <div v-if="showAdd" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="showAdd = false"></div>
      <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <h3 class="text-base font-semibold text-gray-800">Add Subject</h3>
          <button @click="showAdd = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="space-y-4 p-5">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Name <span class="text-red-600">*</span></label>
              <input v-model="add.name" required type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Code <span class="text-red-600">*</span></label>
              <input v-model="add.code" required type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              <p v-if="errors.code" class="mt-1 text-xs text-red-600">{{ errors.code }}</p>
            </div>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Assign classes</label>
            <div class="mb-2 flex items-end justify-between gap-2">
              <div class="flex-1">
                <input v-model="clsFilter" type="text" placeholder="Filter classes..." class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              </div>
              <div class="flex gap-2">
                <button type="button" @click="add.class_ids = filteredClasses.map(c=>c.id)" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50">Select all</button>
                <button type="button" @click="add.class_ids = []" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50">Clear</button>
              </div>
            </div>
            <div class="flex max-h-48 flex-wrap gap-2 overflow-auto rounded-md border border-gray-200 p-2">
              <label v-for="c in filteredClasses" :key="c.id" class="flex items-center gap-2 rounded-md bg-gray-50 px-2 py-1 text-sm text-gray-700 ring-1 ring-inset ring-gray-200">
                <input type="checkbox" :value="c.id" v-model="add.class_ids" /> {{ c.name }}
              </label>
              <div v-if="filteredClasses.length===0" class="px-2 py-1 text-xs text-gray-500">No classes found.</div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-2 border-t px-5 py-3">
          <button @click="showAdd = false" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
          <button :disabled="adding" @click="submitAdd" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ adding ? 'Saving...' : 'Save' }}</button>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showEdit" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="showEdit = false"></div>
      <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <h3 class="text-base font-semibold text-gray-800">Edit Subject</h3>
          <button @click="showEdit = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="space-y-4 p-5">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Name <span class="text-red-600">*</span></label>
              <input v-model="edit.name" required type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Code <span class="text-red-600">*</span></label>
              <input v-model="edit.code" required type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
            </div>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Assign classes</label>
            <div class="flex max-h-48 flex-wrap gap-2 overflow-auto rounded-md border border-gray-200 p-2">
              <label v-for="c in classes" :key="c.id" class="flex items-center gap-2 rounded-md bg-gray-50 px-2 py-1 text-sm text-gray-700 ring-1 ring-inset ring-gray-200">
                <input type="checkbox" :value="c.id" v-model="edit.class_ids" /> {{ c.name }}
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
          <h3 class="text-base font-semibold text-gray-800">Delete Subject</h3>
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

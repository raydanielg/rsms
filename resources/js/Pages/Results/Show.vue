<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  exam: { type: Object, required: true },
  classes: { type: Array, required: true },
  types: { type: Array, required: true },
  students: { type: Array, default: () => [] },
});

const page = usePage();
const errors = computed(() => page.props.errors || {});

const form = ref({
  name: props.exam.name,
  type: props.exam.type,
  year: props.exam.year,
  term: props.exam.term,
  class_ids: (props.exam.classes || []).map(c => c.id),
});
const saving = ref(false);
async function save() {
  saving.value = true;
  try {
    await router.patch(route('results.exams.update', { id: props.exam.id }), form.value, {
      preserveScroll: true,
      onSuccess: () => { window.__toast && window.__toast('success','Exam updated'); },
      onError: (e) => { const m = Object.values(e||{})[0] || 'Failed to update'; window.__toast && window.__toast('error', String(m)); },
    });
  } finally { saving.value = false; }

// Pick first class by default on load
onMounted(() => {
  if (!selectedClass.value && Array.isArray(props.exam.classes) && props.exam.classes.length) {
    selectedClass.value = props.exam.classes[0].name;
  }
});
}

const deleting = ref(false);
async function removeExam() {
  if (!confirm('Delete this exam?')) return;
  deleting.value = true;
  try {
    await router.delete(route('results.exams.destroy', { id: props.exam.id }), {
      preserveScroll: true,
      onSuccess: () => { window.__toast && window.__toast('success','Exam deleted'); },
      onError: () => { window.__toast && window.__toast('error','Failed to delete'); },
    });
  } finally { deleting.value = false; }
}

// Class chips filter for students
const selectedClass = ref('');
const classChips = computed(() => {
  const cls = (props.exam.classes || []).map(c => c.name);
  const counts = {};
  for (const s of props.students || []) {
    counts[s.class_name] = (counts[s.class_name] || 0) + 1;
  }
  return cls.map(n => ({ name: n, count: counts[n] || 0 }));
});
const filteredStudents = computed(() => {
  const list = props.students || [];
  if (!selectedClass.value) return list;
  return list.filter(s => s.class_name === selectedClass.value);
});

// Marks grid state (subjects columns, real-time auto-save)
const subjects = ref([]); // [{id,name,code}]
const marks = ref({}); // key: `${student_id}_${subject_id}` -> number|null
const savingCell = ref({}); // key -> boolean
let saveDebounce;

async function loadMarksForSelected() {
  if (!selectedClass.value) { subjects.value = []; marks.value = {}; return; }
  try {
    const url = route('results.exams.marks', { examId: props.exam.id, class_name: selectedClass.value });
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) { window.__toast && window.__toast('error','Failed to load marks'); return; }
    const data = await res.json();
    subjects.value = data.subjects || [];
    marks.value = data.marks || {};
  } catch (e) {
    window.__toast && window.__toast('error','Failed to load marks');
  }
}

watch(selectedClass, () => { loadMarksForSelected(); });

function cellKey(stuId, subId) { return `${stuId}_${subId}`; }
function getMark(stuId, subId) {
  const v = marks.value[cellKey(stuId, subId)];
  return v === undefined || v === null ? '' : String(v);
}
function setMark(stuId, subId, val) {
  const key = cellKey(stuId, subId);
  const num = val === '' ? null : Math.max(0, Math.min(100, Number(val)));
  marks.value[key] = num;
  savingCell.value[key] = true;
  queueSave([{ student_id: stuId, subject_id: subId, marks: num }]);
}

function queueSave(entries) {
  if (saveDebounce) clearTimeout(saveDebounce);
  // batch multiple quick edits
  if (!queueSave._buf) queueSave._buf = [];
  queueSave._buf.push(...entries);
  saveDebounce = setTimeout(async () => {
    const buf = (queueSave._buf || []).splice(0);
    if (buf.length === 0) return;
    try {
      await fetch(route('results.exams.marks.save', { examId: props.exam.id }), {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]')?.getAttribute('content')) || '' },
        body: JSON.stringify({ class_name: selectedClass.value, entries: buf })
      });
      // clear saving flags
      for (const e of buf) { savingCell.value[cellKey(e.student_id, e.subject_id)] = false; }
    } catch (e) {
      window.__toast && window.__toast('error','Failed to save');
      for (const it of buf) { savingCell.value[cellKey(it.student_id, it.subject_id)] = false; }
    }
  }, 400);
}

// Compute totals/average/division/position
function computeRowTotal(stu) {
  let sum = 0; let count = 0;
  for (const sub of subjects.value) {
    const v = marks.value[cellKey(stu.id, sub.id)];
    if (typeof v === 'number') { sum += v; count++; }
  }
  return { sum, count, avg: count ? sum / count : 0 };
}
function divisionFromAvg(avg) {
  if (avg >= 75) return 'Div I';
  if (avg >= 65) return 'Div II';
  if (avg >= 55) return 'Div III';
  if (avg >= 40) return 'Div IV';
  return 'Fail';
}
const positioned = computed(() => {
  const rows = (filteredStudents.value || []).map(s => {
    const m = computeRowTotal(s); return { id: s.id, sum: m.sum, avg: m.avg };
  });
  rows.sort((a,b) => b.sum - a.sum);
  let lastSum = null, lastRank = 0;
  return rows.reduce((map, r, idx) => {
    const rank = (r.sum === lastSum) ? lastRank : (idx + 1);
    lastSum = r.sum; lastRank = rank; map[r.id] = { rank, sum: r.sum, avg: r.avg, div: divisionFromAvg(r.avg) }; return map;
  }, {});
});

// Keyboard navigation helpers
function onCellKey(e, rIdx, cIdx) {
  const dir = (e.key === 'Enter') ? (e.shiftKey ? -1 : 1) : 0;
  if (dir !== 0) {
    e.preventDefault();
    const next = rIdx + dir;
    if (next >= 0) {
      const nextEl = document.getElementById(`cell-${next}-${cIdx}`);
      if (nextEl) nextEl.focus();
    }
  }
}
</script>

<template>
  <Head :title="`Exam: ${exam.name}`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Exam: {{ exam.name }}</h2>
        <div class="flex gap-2">
          <button :disabled="saving" @click="save" class="rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ saving ? 'Saving...' : 'Save' }}</button>
          <button :disabled="deleting" @click="removeExam" class="rounded-md bg-rose-600 px-3 py-2 text-sm font-medium text-white hover:bg-rose-700 disabled:opacity-60">{{ deleting ? 'Deleting...' : 'Delete' }}</button>
        </div>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Assigned Classes filter chips -->
      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="mb-2 text-sm font-semibold text-gray-700">Assigned classes</div>
        <div class="flex flex-wrap gap-2">
          <button type="button" @click="selectedClass = ''" :class="['rounded-full px-3 py-1 text-xs', selectedClass==='' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">
            All ({{ (students || []).length }})
          </button>
          <button v-for="c in classChips" :key="c.name" type="button" @click="selectedClass = c.name"
                  :class="['rounded-full px-3 py-1 text-xs', selectedClass===c.name ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">
            {{ c.name }} ({{ c.count }})
          </button>
        </div>
      </div>

      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Exam name <span class="text-red-600">*</span></label>
            <input v-model="form.name" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
            <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Type <span class="text-red-600">*</span></label>
            <select v-model="form.type" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Year <span class="text-red-600">*</span></label>
            <input v-model="form.year" type="number" min="2000" max="2100" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Term <span class="text-red-600">*</span></label>
            <select v-model="form.term" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option :value="1">1</option>
              <option :value="2">2</option>
              <option :value="3">3</option>
            </select>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <div class="text-sm font-semibold text-gray-700">Assign classes</div>
          <div class="text-xs text-gray-500">Selected: {{ form.class_ids.length }}</div>
        </div>
        <div class="flex max-h-60 flex-wrap gap-2 overflow-auto rounded-md border border-gray-200 p-2">
          <label v-for="c in classes" :key="c.id" class="flex items-center gap-2 rounded-md bg-gray-50 px-2 py-1 text-sm text-gray-700 ring-1 ring-inset ring-gray-200">
            <input type="checkbox" :value="c.id" v-model="form.class_ids" /> {{ c.name }}
          </label>
        </div>
      </div>

      <!-- Marks entry grid (real-time auto-save) -->
      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="mb-2 text-sm font-semibold text-gray-700">Marks {{ selectedClass ? `- ${selectedClass}` : '' }}</div>
        <div v-if="!selectedClass" class="text-sm text-gray-500">Select a class chip above to enter marks.</div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Reg No</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Name</th>
                <th v-for="(sub, sIdx) in subjects" :key="sub.id" class="px-3 py-2 text-center font-semibold text-gray-700">{{ sub.code || sub.name }}</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Total</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Average</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Position</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Division</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="(stu, rIdx) in filteredStudents" :key="stu.id" class="hover:bg-gray-50">
                <td class="px-3 py-2 text-gray-900 whitespace-nowrap">{{ stu.reg_no }}</td>
                <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ stu.name }}</td>
                <td v-for="(sub, cIdx) in subjects" :key="sub.id" class="px-2 py-1 text-center">
                  <input :id="`cell-${rIdx}-${cIdx}`" type="number" min="0" max="100" step="1"
                         :value="getMark(stu.id, sub.id)" @input="(e) => setMark(stu.id, sub.id, e.target.value)"
                         @keydown="(e) => onCellKey(e, rIdx, cIdx)"
                         class="w-16 rounded-md border-gray-300 text-center text-sm shadow-sm focus:border-green-600 focus:ring-green-600"
                  />
                </td>
                <td class="px-3 py-2 text-center text-gray-700">{{ computeRowTotal(stu).sum.toFixed(0) }}</td>
                <td class="px-3 py-2 text-center text-gray-700">{{ computeRowTotal(stu).avg.toFixed(1) }}</td>
                <td class="px-3 py-2 text-center text-gray-700">{{ (positioned[stu.id]?.rank) || '-' }}</td>
                <td class="px-3 py-2 text-center text-gray-700">{{ (positioned[stu.id]?.div) || '-' }}</td>
              </tr>
              <tr v-if="filteredStudents.length === 0">
                <td :colspan="4 + (subjects.length || 0)" class="px-4 py-8 text-center text-sm text-gray-500">No students found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

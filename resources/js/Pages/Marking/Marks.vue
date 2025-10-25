<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
  exam: { type: Object, required: true },
});

const selectedClassId = ref(null);
const loading = ref(false);
const students = ref([]);
const subjects = ref([]);
const marks = ref({});
const saving = ref(false);
const savedKeys = ref(new Set());
const savingKeys = ref(new Set());
const debounceTimers = new Map();
const dirtyKeys = ref(new Set());
const autoSave = ref(true);
let lastFocused = { studentId: null, subjectId: null };

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  if (meta && meta.getAttribute('content')) return meta.getAttribute('content');
  // Fallback to XSRF-TOKEN cookie (Laravel)
  const m = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
  return m ? decodeURIComponent(m[1]) : '';
}

// (removed custom arrow adjust to use native spinner/arrow behavior)

function scheduleSave(studentId, subjectId, delay = 0) {
  const k = keyFor(studentId, subjectId);
  console.log('Scheduling save for', k);
  if (debounceTimers.has(k)) clearTimeout(debounceTimers.get(k));
  const t = setTimeout(() => {
    console.log('Auto-saving', k);
    saveSingle(studentId, subjectId);
    debounceTimers.delete(k);
  }, delay);
  debounceTimers.set(k, t);
}

function onTab(siRow, siSubject, backwards, event) {
  event.preventDefault();
  const s = students.value[siRow];
  if (s) {
    saveSingle(s.id, subjects.value[siSubject].id);
  }
  let nextRow = siRow;
  let nextSub = backwards ? siSubject - 1 : siSubject + 1;
  if (nextSub < 0) { nextSub = subjects.value.length - 1; nextRow = Math.max(0, siRow - 1); }
  if (nextSub >= subjects.value.length) { nextSub = 0; nextRow = Math.min(students.value.length - 1, siRow + 1); }
  const nextStudent = students.value[nextRow];
  const nextSubject = subjects.value[nextSub];
  if (nextStudent && nextSubject) {
    const id = getInputId(nextStudent.id, nextSubject.id);
    setTimeout(() => {
      const el = document.getElementById(id);
      if (el) { el.focus(); el.select && el.select(); }
    }, 0);
  }
}

async function loadClass(clsId) {
  selectedClassId.value = clsId;
  loading.value = true;
  try {
    const url = `/results/exams/${props.exam.id}/marks?class_id=${encodeURIComponent(clsId)}`;
    const res = await fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrfToken()
      }
    });
    const data = res.ok ? await res.json() : { students: [] };
    students.value = data.students || [];
    subjects.value = data.subjects || [];
    marks.value = Object.assign({}, data.marks || {});
  } finally {
    loading.value = false;
  }

async function saveSingle(studentId, subjectId) {
  if (!selectedClassName.value) return;
  if (isInvalidCell(studentId, subjectId)) { window.__toast && window.__toast('error','Invalid mark (0-100)'); return; }
  const k = keyFor(studentId, subjectId);
  const val = marks.value[k];
  const body = { class_name: selectedClassName.value, entries: [ { student_id: studentId, subject_id: subjectId, marks: val === '' ? null : val } ] };
  try {
    savingKeys.value.add(k);
    const url = `/results/exams/${props.exam.id}/marks`;
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': getCsrfToken() },
      credentials: 'same-origin',
      body: JSON.stringify(body)
    });
    if (!res.ok) throw new Error('save failed');
    // visual feedback for saved cell
    savedKeys.value.add(k);
    setTimeout(() => { savedKeys.value.delete(k); }, 800);
    dirtyKeys.value.delete(k);
    return true;
  } catch (e) {
    if (window.__toast) window.__toast('error','Failed to auto-save'); else alert('Failed to auto-save');
    return false;
  }
  finally {
    savingKeys.value.delete(k);
  }
}

function onEnter(studentIndex, subjectId) {
  const nextIndex = Math.min(studentIndex + 1, students.value.length - 1);
  const currentStudent = students.value[studentIndex];
  if (currentStudent) {
    // fire-and-forget save, focus moves immediately
    saveSingle(currentStudent.id, subjectId);
  }
  if (nextIndex >= 0) {
    const nextStudent = students.value[nextIndex];
    if (nextStudent) {
      const nextId = getInputId(nextStudent.id, subjectId);
      setTimeout(() => {
        const el = document.getElementById(nextId);
        if (el) {
          el.focus(); el.select && el.select();
        } else {
          // DOM fallback: find next row and same subject by data attributes
          const current = document.activeElement;
          const row = current?.closest('tr')?.nextElementSibling;
          if (row) {
            const target = row.querySelector(`[data-subject="${subjectId}"]`);
            if (target) { target.focus(); target.select && target.select(); }
          }
        }
      }, 0);
    }
  }
}

// Global Enter handler as fallback
function onGlobalEnter(e) {
  const active = document.activeElement;
  if (!active || !active.closest('tbody')) return;

  // Save current cell before moving
  const dataStudent = active.getAttribute('data-student');
  const dataSubject = active.getAttribute('data-subject');
  if (dataStudent && dataSubject) {
    saveSingle(dataStudent, dataSubject);
  }

  const row = active.closest('tr');
  const inputs = Array.from(row.querySelectorAll('input[type="text"]'));
  const currentIndex = inputs.indexOf(active);

  if (e.shiftKey) {
    // Shift+Enter - go up
    if (currentIndex > 0) {
      inputs[currentIndex - 1].focus();
    } else {
      const prevRow = row.previousElementSibling;
      if (prevRow) {
        const prevInputs = Array.from(prevRow.querySelectorAll('input[type="text"]'));
        if (prevInputs.length > 0) {
          prevInputs[prevInputs.length - 1].focus();
        }
      }
    }
  } else {
    // Enter - go down
    if (currentIndex < inputs.length - 1) {
      inputs[currentIndex + 1].focus();
    } else {
      const nextRow = row.nextElementSibling;
      if (nextRow) {
        const nextInputs = Array.from(nextRow.querySelectorAll('input[type="text"]'));
        if (nextInputs.length > 0) {
          nextInputs[0].focus();
        }
      }
    }
  }
}

function onCellKey(e, rIdx, cIdx) {
  const dir = (e.key === 'Enter') ? (e.shiftKey ? -1 : 1) : 0;
  if (dir !== 0) {
    e.preventDefault();

    // Save current cell
    const currentStudent = students.value[rIdx];
    const currentSubject = subjects.value[cIdx];
    if (currentStudent && currentSubject) {
      saveSingle(currentStudent.id, currentSubject.id);
    }

    const next = rIdx + dir;
    if (next >= 0 && next < students.value.length) {
      const nextEl = document.getElementById(`mark-s${students.value[next].id}-p${subjects.value[cIdx].id}`);
      if (nextEl) nextEl.focus();
    }
  }
}

function onCellFocus(siRow, subjectId, ev) {
  // Save previously focused cell if it has unsaved changes
  if (lastFocused.studentId && lastFocused.subjectId) {
    const k = keyFor(lastFocused.studentId, lastFocused.subjectId);
    if (dirtyKeys.value.has(k)) {
      saveSingle(lastFocused.studentId, lastFocused.subjectId);
    }
  }
  const s = students.value[siRow];
  if (s) {
    lastFocused = { studentId: s.id, subjectId };
  }
  ev.target.select && ev.target.select();
}

function onPaste(siRow, siSubject, ev) {
  const text = ev.clipboardData && ev.clipboardData.getData('text');
  if (!text) return;
  ev.preventDefault();
  // Split rows by newline and columns by tab or comma
  const rows = text.split(/\r?\n/).filter(Boolean);
  for (let r = 0; r < rows.length && (siRow + r) < students.value.length; r++) {
    const cols = rows[r].split(/\t|,/);
    for (let c = 0; c < cols.length && (siSubject + c) < subjects.value.length; c++) {
      const s = students.value[siRow + r];
      const sub = subjects.value[siSubject + c];
      let v = cols[c].trim();
      if (v === '') continue;
      let num = Number(v);
      if (Number.isNaN(num)) continue;
      if (num < 0) num = 0;
      if (num > 100) num = 100;
      marks.value[keyFor(s.id, sub.id)] = num;
      dirtyKeys.value.add(keyFor(s.id, sub.id));
      if (autoSave.value) scheduleSave(s.id, sub.id, 150);
    }
  }
}

function onTabVertical(siRow, subjectId, direction) {
  // Save current cell, then move vertically in same subject
  const currentStudent = students.value[siRow];
  if (currentStudent) {
    saveSingle(currentStudent.id, subjectId);
  }
  const delta = direction === 'up' ? -1 : 1;
  const target = Math.min(Math.max(siRow + delta, 0), students.value.length - 1);
  const nextStudent = students.value[target];
  if (nextStudent) {
    const id = getInputId(nextStudent.id, subjectId);
    setTimeout(() => {
      const el = document.getElementById(id);
      if (el) { el.focus(); el.select && el.select(); }
    }, 0);
  }
}
}

// Validation helpers
function isInvalidCell(studentId, subjectId) {
  const val = marks.value[keyFor(studentId, subjectId)];
  if (val === null || val === undefined || val === '') return false;
  if (typeof val !== 'number') return true;
  return val < 0 || val > 100;
}
const hasInvalid = computed(() => {
  for (const s of students.value) {
    for (const sub of subjects.value) {
      if (isInvalidCell(s.id, sub.id)) return true;
    }
  }
  return false;
});

// Derived aggregates
const subjectCount = computed(() => (subjects.value || []).length || 0);
function getMark(studentId, subjectId) {
  const val = marks.value[keyFor(studentId, subjectId)];
  return typeof val === 'number' ? val : null;
}
const totals = computed(() => {
  const map = {};
  for (const s of students.value) {
    let sum = 0;
    for (const sub of subjects.value) {
      const m = getMark(s.id, sub.id);
      if (typeof m === 'number') sum += m;
    }
    map[s.id] = sum;
  }
  return map;
});
const averages = computed(() => {
  const map = {};
  const cnt = subjectCount.value || 1;
  for (const s of students.value) {
    map[s.id] = totals.value[s.id] / cnt;
  }
  return map;
});
const positions = computed(() => {
  // Dense rank by total descending
  const entries = students.value.map(s => ({ id: s.id, total: totals.value[s.id] || 0 }));
  entries.sort((a,b) => b.total - a.total);
  const posMap = {};
  let rank = 0, last = null;
  for (const e of entries) {
    if (last === null || e.total < last) rank += 1;
    posMap[e.id] = rank;
    last = e.total;
  }
  return posMap;
});
function divisionFromAverage(avg) {
  if (avg >= 75) return 'I';
  if (avg >= 60) return 'II';
  if (avg >= 45) return 'III';
  if (avg >= 30) return 'IV';
  return '0';
}
// end aggregates

function gradeFromAverage(avg) {
  if (avg >= 75) return 'A';        // 75 - 100
  if (avg >= 65) return 'B';        // 65 - 74
  if (avg >= 45) return 'C';        // 45 - 64
  if (avg >= 30) return 'D';        // 30 - 44
  return 'F';                       // < 30
}

onMounted(() => {
  const first = (props.exam.classes || [])[0];
  if (first && first.id) loadClass(first.id);
});

function keyFor(studentId, subjectId) {
  return `${studentId}_${subjectId}`;
}

function getInputId(studentId, subjectId) {
  return `mark-s${studentId}-p${subjectId}`;
}

function maybeAutoSave(studentId, subjectId) {
  if (autoSave.value) {
    saveSingle(studentId, subjectId);
  }
}

// (removed key filters to allow native number input behavior)

function onInputMark(studentId, subjectId, ev) {
  const v = ev.target.value;
  if (v === '' || v === null) {
    marks.value[keyFor(studentId, subjectId)] = null;
    dirtyKeys.value.add(keyFor(studentId, subjectId));
    return;
  }
  let num = Number(v);
  if (Number.isNaN(num)) {
    marks.value[keyFor(studentId, subjectId)] = null;
    dirtyKeys.value.add(keyFor(studentId, subjectId));
    return;
  }
  if (num < 0) {
    num = 0;
    window.__toast && window.__toast('warning','Marks cannot be below 0');
  }
  if (num > 100) {
    num = 100;
    window.__toast && window.__toast('warning','Marks cannot exceed 100');
  }
  marks.value[keyFor(studentId, subjectId)] = num;
  dirtyKeys.value.add(keyFor(studentId, subjectId));
  scheduleSave(studentId, subjectId);
}

const selectedClassName = computed(() => {
  const cls = (props.exam.classes || []).find(c => c.id === selectedClassId.value);
  return cls ? cls.name : null;
});

async function saveAll() {
  if (!selectedClassName.value) return;
  if (hasInvalid.value) { window.__toast && window.__toast('error','Fix invalid marks first'); return; }
  saving.value = true;
  try {
    const entries = [];
    for (const s of students.value) {
      for (const sub of subjects.value) {
        const k = keyFor(s.id, sub.id);
        if (k in marks.value) {
          const val = marks.value[k];
          entries.push({ student_id: s.id, subject_id: sub.id, marks: val === '' ? null : val });
        }
      }
    }
    const res = await fetch(`/results/exams/${props.exam.id}/marks`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({ class_name: selectedClassName.value, entries })
    });
    if (res.ok) {
      if (window.__toast) window.__toast('success','Marks saved'); else alert('Marks saved');
    } else {
      if (window.__toast) window.__toast('error','Failed to save'); else alert('Failed to save');
    }
  } catch (e) {
    if (window.__toast) window.__toast('error','Failed to save'); else alert('Failed to save');
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <Head :title="`Marks - ${exam.name}`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Marks: {{ exam.name }} ({{ exam.year }}/T{{ exam.term }})
        </h2>
      </div>
    </template>

    <div class="space-y-4">
      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="mb-3 text-sm font-medium text-gray-700">Classes</div>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="c in exam.classes || []"
            :key="c.id"
            type="button"
            @click="loadClass(c.id)"
            class="rounded-md px-3 py-1.5 text-sm ring-1 ring-inset"
            :class="selectedClassId===c.id ? 'bg-green-600 text-white ring-green-600' : 'bg-white text-gray-700 ring-gray-300 hover:bg-gray-50'"
          >
            {{ c.name }}
          </button>
          <div v-if="!exam.classes || exam.classes.length===0" class="text-sm text-gray-500">No classes assigned to this exam.</div>
        </div>
      </div>

      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="mb-3 flex items-center justify-between">
          <div class="text-sm font-medium text-gray-700">Students</div>
          <div class="flex items-center gap-3">
            <div class="text-xs text-gray-500">{{ students.length }} total</div>
            <span v-if="hasInvalid" class="text-xs font-medium text-rose-700">Fix invalid marks first</span>
            <button type="button" @click="saveAll" :disabled="saving || hasInvalid"
                    class="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-green-700 disabled:opacity-60">
              <svg v-if="saving" class="mr-1.5 h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="2" opacity="0.25"/><path d="M21 12a9 9 0 0 1-9 9" stroke-width="2"/></svg>
              Save All
            </button>
          </div>
        </div>
        <div v-if="loading" class="text-sm text-gray-500">Loading...</div>
        <div v-else>
          <div v-if="students.length===0" class="text-sm text-gray-500">Select a class to load students.</div>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm" @keydown.enter.stop.prevent="onGlobalEnter" tabindex="0">
              <thead class="bg-gray-50">
                <tr>
                  <th class="sticky top-0 left-0 z-20 bg-gray-50 px-3 py-2 text-left font-semibold text-gray-700 border-r border-gray-200">Reg No</th>
                  <th class="sticky top-0 left-28 z-20 bg-gray-50 px-3 py-2 text-left font-semibold text-gray-700 border-r border-gray-200">Full Name</th>
                  <th v-for="sub in subjects" :key="sub.id" :title="sub.name || ''" class="sticky top-0 z-10 bg-gray-50 px-2 py-2 text-center font-semibold text-gray-700 whitespace-nowrap border-r border-gray-200">
                    {{ (sub.name || '').toString().slice(0,12) }}
                  </th>
                  <th class="sticky top-0 z-10 bg-gray-50 px-2 py-2 text-center font-semibold text-gray-700 whitespace-nowrap border-l border-gray-200">Total</th>
                  <th class="sticky top-0 z-10 bg-gray-50 px-2 py-2 text-center font-semibold text-gray-700 whitespace-nowrap">Grade</th>
                  <th class="sticky top-0 z-10 bg-gray-50 px-2 py-2 text-center font-semibold text-gray-700 whitespace-nowrap">Average</th>
                  <th class="sticky top-0 z-10 bg-gray-50 px-2 py-2 text-center font-semibold text-gray-700 whitespace-nowrap">Position</th>
                  <th class="sticky top-0 z-10 bg-gray-50 px-2 py-2 text-center font-semibold text-gray-700 whitespace-nowrap">Division</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(s, siRow) in students" :key="s.id" class="odd:bg-gray-50 hover:bg-gray-50">
                  <td class="sticky left-0 bg-white px-3 py-2 text-gray-900 whitespace-nowrap border-t border-r border-gray-200">{{ s.reg_no }}</td>
                  <td class="sticky left-28 bg-white px-3 py-2 text-gray-700 whitespace-nowrap border-t border-r border-gray-200">{{ s.name }}</td>
                  <td v-for="(sub, si) in subjects" :key="sub.id" class="px-2 py-1 text-center border-t border-r border-gray-200">
                    <div class="relative inline-block">
                      <input
                        type="number"
                        inputmode="numeric"
                        min="0"
                        max="100"
                        step="1"
                        :value="marks[keyFor(s.id, sub.id)] ?? ''"
                        placeholder=""
                        @input="onInputMark(s.id, sub.id, $event)"
                        @change="maybeAutoSave(s.id, sub.id)"
                        @blur="maybeAutoSave(s.id, sub.id)"
                        @keydown.enter.prevent="onTabVertical(siRow, sub.id, 'down')"
                        @keydown.tab.prevent="onTabVertical(siRow, sub.id, 'down')"
                        @keydown.shift.tab.prevent="onTabVertical(siRow, sub.id, 'up')"
                        @paste="onPaste(siRow, si, $event)"
                        @focus="onCellFocus(siRow, sub.id, $event)"
                      :data-student="s.id"
                      :data-subject="sub.id"
                        :class="[
                          'marks-cell w-14 rounded-md border bg-white px-2 py-1 text-center text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-300 transition-shadow',
                          isInvalidCell(s.id, sub.id) ? 'border-rose-400 text-rose-700' : 'border-gray-300 text-gray-900',
                          savedKeys.has(keyFor(s.id, sub.id)) ? 'ring-2 ring-emerald-400' : '',
                          savingKeys.has(keyFor(s.id, sub.id)) ? 'opacity-70 cursor-wait' : ''
                        ]"
                        :disabled="savingKeys.has(keyFor(s.id, sub.id))"
                      />
                      <span v-if="savedKeys.has(keyFor(s.id, sub.id))" class="pointer-events-none absolute -right-1 -top-1 inline-flex h-3.5 w-3.5 items-center justify-center rounded-full bg-emerald-500 text-white">
                        <svg viewBox="0 0 20 20" fill="none" class="h-2.5 w-2.5"><path d="M5 10l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                      </span>
                      <span v-else-if="savingKeys.has(keyFor(s.id, sub.id))" class="pointer-events-none absolute -right-1 -top-1 inline-flex h-3.5 w-3.5 items-center justify-center rounded-full bg-blue-500/90 text-white">
                        <svg class="h-2.5 w-2.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <circle cx="12" cy="12" r="9" stroke-opacity="0.25" />
                          <path d="M21 12a9 9 0 0 1-9 9" stroke-linecap="round" />
                        </svg>
                      </span>
                    </div>
                  </td>
                  <td class="px-2 py-1 text-center text-gray-900 font-medium border-t border-l border-gray-200">{{ totals[s.id] ?? 0 }}</td>
                  <td class="px-2 py-1 text-center text-gray-700 border-t border-gray-200">{{ gradeFromAverage(averages[s.id] ?? 0) }}</td>
                  <td class="px-2 py-1 text-center text-gray-700 border-t border-gray-200">{{ (averages[s.id] ?? 0).toFixed(1) }}</td>
                  <td class="px-2 py-1 text-center text-gray-700 border-t border-gray-200">{{ positions[s.id] ?? '-' }}</td>
                  <td class="px-2 py-1 text-center text-gray-700 border-t border-gray-200">{{ divisionFromAverage(averages[s.id] ?? 0) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
</style>

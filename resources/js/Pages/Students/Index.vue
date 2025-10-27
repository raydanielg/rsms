<script setup>
import { ref, computed, watch, reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  students: { type: Object, required: true },
  classes: { type: Array, required: true },
  all_classes: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({ q: '', class: '', sex: '' }) },
  school_number: { type: String, default: '' },
  class_subjects: { type: Object, default: () => ({}) },
});

// Local filter state synced with server via GET
const q = ref(props.filters.q || '');
const fClass = ref(props.filters.class || '');
const fSex = ref(props.filters.sex || '');

function submitFilters(pageUrl = route('students.index')) {
  router.get(pageUrl, { q: q.value, class: fClass.value, sex: fSex.value }, { preserveState: true, preserveScroll: true, replace: true });
}

// Delete modal state
const showDelete = ref(false);
const del = ref({ id: null, name: '', reg_no: '' });
const deleting = ref(false);
function openDelete(item) {
  del.value = { id: item.id || null, name: item.name, reg_no: item.reg_no };
  showDelete.value = true;
}
async function confirmDelete() {
  deleting.value = true;
  try {
    // Resolve ID by reg_no if missing
    if (!del.value.id && del.value.reg_no) {
      try {
        const res = await fetch(route('students.show.json', { reg_no: del.value.reg_no }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (res.ok) {
          const s = await res.json();
          del.value.id = s.id;
        }
      } catch (e) {}
    }
    if (!del.value.id) { window.__toast && window.__toast('error', 'Missing student ID'); return; }
    await router.delete(route('students.destroy', { id: del.value.id }), {
      preserveScroll: true,
      onSuccess: () => { showDelete.value = false; window.__toast && window.__toast('success', 'Student deleted'); },
      onError: () => { window.__toast && window.__toast('error', 'Failed to delete'); }
    });
  } finally {
    deleting.value = false;
  }
}

async function removeInlineSubject(student, subject) {
  if (!student?.id || !subject?.id) return;
  const key = inlineKey(student.id, subject.id);
  setInlineRemoving(key, true);
  try {
    const url = route('students.subjects.detach', { student: student.id, subject: subject.id });
    const res = await fetch(url, {
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken(),
      },
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      window.__toast && window.__toast('error', data.message || 'Imeshindikana kuondoa somo.');
      return;
    }
    recordInlineRemoval(student.id, subject);
    syncListAfterChange();
    window.__toast && window.__toast('success', 'Somo limeondolewa.');
  } catch (e) {
    window.__toast && window.__toast('error', e instanceof Error ? e.message : 'Imeshindikana kuondoa somo.');
  } finally {
    setInlineRemoving(key, false);
  }
}

async function addInlineSubject(student, subject) {
  if (!student?.id || !subject?.id) {
    window.__toast && window.__toast('error', 'Haikuwezekana kutambua mwanafunzi au somo.');
    return;
  }
  const key = inlineKey(student.id, subject.id);
  setInlineAdding(key, true);
  try {
    const url = route('students.subjects.attach', { student: student.id });
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken(),
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({ subject_id: subject.id }),
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      window.__toast && window.__toast('error', data.message || 'Imeshindikana kuongeza somo.');
      return;
    }
    removeInlineHistory(student.id, subject.id);
    syncListAfterChange();
    window.__toast && window.__toast('success', 'Somo limeongezwa tena.');
  } catch (e) {
    window.__toast && window.__toast('error', e instanceof Error ? e.message : 'Imeshindikana kuongeza somo.');
  } finally {
    setInlineAdding(key, false);
  }
}

// Edit Student modal state
const showEdit = ref(false);
const editLoading = ref(false);
const edit = ref({
  id: null,
  name: '',
  sex: 'M',
  class_name: '',
  dob: '',
  academic_year: new Date().getFullYear(),
  guardian_name: '',
  guardian_phone: '',
  guardian_relation: '',
  exam_no: '',
  photo: null,
});
function onEditPhoto(e) { edit.value.photo = e.target.files?.[0] ?? null; }
async function openEdit(item) {
  showEdit.value = true;
  editLoading.value = true;
  detailsError.value = '';
  try {
    // fetch latest full details
    const payload = item.id ? { id: item.id } : { reg_no: item.reg_no };
    const url = route('students.show.json', payload);
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const s = res.ok ? await res.json() : null;
    if (!s) { detailsError.value = 'Failed to load student for editing.'; window.__toast && window.__toast('error', 'Failed to load student'); return; }
    edit.value = {
      id: s.id,
      name: s.name || '',
      sex: s.sex || 'M',
      class_name: s.class_name || '',
      dob: s.dob || '',
      academic_year: s.academic_year || new Date().getFullYear(),
      guardian_name: s.guardian_name || '',
      guardian_phone: s.guardian_phone || '',
      guardian_relation: s.guardian_relation || '',
      exam_no: s.exam_no || prefix.value,
      photo: null,
    };
  } finally {
    editLoading.value = false;
  }
}
const editing = ref(false);
async function submitEdit() {
  if (!edit.value.id) return;
  const fd = new FormData();
  Object.entries(edit.value).forEach(([k, v]) => {
    if (k === 'id') return;
    if (v !== null && v !== undefined && v !== '') fd.append(k, v);
  });
  fd.append('_method', 'PATCH');
  editing.value = true;
  try {
    await router.post(route('students.update', { id: edit.value.id }), fd, {
      preserveScroll: true,
      onSuccess: () => { showEdit.value = false; window.__toast && window.__toast('success', 'Student updated successfully'); },
      onError: (errs) => { const msg = Object.values(errs||{})[0] || 'Failed to update student'; window.__toast && window.__toast('error', String(msg)); },
    });
  } finally {
    editing.value = false;
  }
}

// Export modal state
const showExport = ref(false);
const exp = ref({ format: 'csv', scope: 'current', class_name: '' });
function openExport() {
  exp.value = { format: 'csv', scope: 'current', class_name: fClass.value || '' };
  showExport.value = true;
}
function confirmExport() {
  // Resolve class param based on selection
  let cls = fClass.value || '';
  if (exp.value.scope === 'all') cls = '';
  if (exp.value.scope === 'class' && exp.value.class_name) cls = exp.value.class_name;
  if (exp.value.format === 'csv') {
    const url = route('students.export', { q: q.value, class: cls, sex: fSex.value });
    window.location.href = url;
    showExport.value = false;
  } else {
    alert('PDF export will be enabled after installing barryvdh/laravel-dompdf.');
  }
}

function openAdd() {
  showAdd.value = true;
  // Pre-fill exam prefix if available
  if (prefix.value && !form.value.exam_no) {
    form.value.exam_no = prefix.value;
  }
}

function clearFilters() {
  q.value = '';
  fClass.value = '';
  fSex.value = '';
  submitFilters();
}

function exportCsv() {
  const url = route('students.export', { q: q.value, class: fClass.value, sex: fSex.value });
  window.location.href = url;
}

// Live search suggestions and auto-refresh table
const sug = ref({ open: false, items: [], loading: false, index: -1 });
let qDebounce;
watch(q, (val) => {
  if (qDebounce) clearTimeout(qDebounce);
  qDebounce = setTimeout(async () => {
    // auto refresh table
    submitFilters();
    // fetch suggestions if query present
    if (!val || val.length < 2) { sug.value = { open: false, items: [], loading: false, index: -1 }; return; }
    sug.value.loading = true;
    try {
      const url = route('students.search', { q: val });
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      sug.value.items = res.ok ? await res.json() : [];
      sug.value.open = sug.value.items.length > 0;
    } finally {
      sug.value.loading = false;
    }
  }, 300);
});

const subjectModal = ref({
  open: false,
  loading: false,
  student: null,
  core: [],
  classOptional: [],
  optional: [],
  available: [],
  addId: '',
  saving: false,
  removingId: null,
  quickAddingId: null,
  error: '',
});

const inlineRemoving = ref(new Set());
const inlineAdding = ref(new Set());
const inlineRemovedHistory = reactive(new Map());

function classSubjectsForStudent(student) {
  if (!student) return [];
  const key = student.class_name || '';
  const list = props.class_subjects?.[key] || [];
  return Array.isArray(list) ? list : [];
}

function classCoreSubjectsForStudent(student) {
  const subjects = classSubjectsForStudent(student);
  if (!subjects.length) return [];
  return subjects.filter((sub, idx) => sub.is_core === true || (sub.is_core === undefined && idx < 7)).slice(0, 7);
}

function classElectiveSubjectsForStudent(student) {
  const subjects = classSubjectsForStudent(student);
  if (!subjects.length) return [];
  return subjects.filter((sub, idx) => sub.is_core === false || (sub.is_core === undefined && idx >= 7));
}

function assignedOptionalSubjectsForStudent(student) {
  if (!student) return [];
  const list = student.subjects || [];
  return Array.isArray(list) ? list : [];
}

function classAvailableOptionalSubjectsForStudent(student) {
  const classOptionals = classElectiveSubjectsForStudent(student);
  if (!classOptionals.length) return [];
  const assignedIds = new Set(assignedOptionalSubjectsForStudent(student).map(sub => sub.id));
  return classOptionals.filter(sub => !assignedIds.has(sub.id));
}

function subjectCode(sub) {
  if (!sub) return '—';
  return sub.code || sub.name || '—';
}

function subjectLabel(sub) {
  if (!sub) return '—';
  return sub.name || sub.code || '—';
}

function subjectName(sub) {
  return sub?.name || sub?.code || '—';
}

function recordInlineRemoval(studentId, subject) {
  if (!studentId || !subject?.id) return;
  const list = inlineRemovedHistory.get(studentId) || [];
  if (!list.some(item => item.id === subject.id)) {
    inlineRemovedHistory.set(studentId, [...list, subject]);
  }
}

function inlineRemovedFor(studentId) {
  return inlineRemovedHistory.get(studentId) || [];
}

function removeInlineHistory(studentId, subjectId) {
  if (!studentId || !subjectId) return;
  const list = inlineRemovedHistory.get(studentId) || [];
  const filtered = list.filter(item => item.id !== subjectId);
  inlineRemovedHistory.set(studentId, filtered);
}

function inlineKey(studentId, subjectId) {
  return `${studentId ?? ''}:${subjectId ?? ''}`;
}

function setInlineRemoving(key, value) {
  const next = new Set(inlineRemoving.value);
  if (value) next.add(key); else next.delete(key);
  inlineRemoving.value = next;
}

function isInlineRemoving(studentId, subjectId) {
  return inlineRemoving.value.has(inlineKey(studentId, subjectId));
}

function setInlineAdding(key, value) {
  const next = new Set(inlineAdding.value);
  if (value) next.add(key); else next.delete(key);
  inlineAdding.value = next;
}

function isInlineAdding(studentId, subjectId) {
  return inlineAdding.value.has(inlineKey(studentId, subjectId));
}

function resetSubjectModal() {
  Object.assign(subjectModal.value, {
    open: false,
    loading: false,
    student: null,
    core: [],
    classOptional: [],
    optional: [],
    available: [],
    addId: '',
    saving: false,
    removingId: null,
    error: '',
  });
}

function closeSubjects() {
  subjectModal.value.open = false;
  setTimeout(() => resetSubjectModal(), 200);
}

function applySubjectPayload(payload) {
  if (!payload) return;
  const available = Array.isArray(payload.available) ? payload.available : [];
  Object.assign(subjectModal.value, {
    student: payload.student || subjectModal.value.student,
    core: Array.isArray(payload.core) ? payload.core : [],
    classOptional: Array.isArray(payload.class_optional) ? payload.class_optional : subjectModal.value.classOptional,
    optional: Array.isArray(payload.optional) ? payload.optional : [],
    available,
    addId: available.length ? String(available[0].id) : '',
    quickAddingId: null,
  });
}

async function fetchSubjectPayload(studentId) {
  const url = route('students.subjects', { student: studentId });
  const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  if (!res.ok) {
    const problem = await res.json().catch(() => ({}));
    throw new Error(problem.message || 'Failed to load subjects');
  }
  return res.json();
}

function syncListAfterChange() {
  router.reload({ only: ['students', 'class_subjects'], preserveState: true, preserveScroll: true });
}

async function openSubjects(student) {
  if (!student?.id) {
    window.__toast && window.__toast('error', 'Missing student ID');
    return;
  }
  Object.assign(subjectModal.value, {
    open: true,
    loading: true,
    student: { id: student.id, name: student.name, reg_no: student.reg_no, class_name: student.class_name },
    core: classCoreSubjectsForStudent(student),
    classOptional: classAvailableOptionalSubjectsForStudent(student),
    optional: assignedOptionalSubjectsForStudent(student),
    available: classAvailableOptionalSubjectsForStudent(student),
    addId: '',
    saving: false,
    removingId: null,
    quickAddingId: null,
    error: '',
  });
  try {
    const payload = await fetchSubjectPayload(student.id);
    applySubjectPayload(payload);
  } catch (e) {
    subjectModal.value.error = e instanceof Error ? e.message : 'Failed to load subjects';
  } finally {
    subjectModal.value.loading = false;
  }
}

function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

async function addOptionalSubject() {
  if (!subjectModal.value.student?.id) return;
  if (!subjectModal.value.addId) {
    subjectModal.value.error = 'Chagua somo la kuongeza.';
    return;
  }
  subjectModal.value.saving = true;
  subjectModal.value.error = '';
  try {
    const url = route('students.subjects.attach', { student: subjectModal.value.student.id });
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken(),
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({ subject_id: subjectModal.value.addId }),
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      subjectModal.value.error = data.message || 'Imeshindikana kuongeza somo.';
      return;
    }
    applySubjectPayload(data);
    syncListAfterChange();
    window.__toast && window.__toast('success', 'Somo limeongezwa.');
  } catch (e) {
    subjectModal.value.error = e instanceof Error ? e.message : 'Imeshindikana kuongeza somo.';
  } finally {
    subjectModal.value.saving = false;
  }
}

async function removeOptionalSubject(subjectId) {
  if (!subjectModal.value.student?.id) return;
  subjectModal.value.error = '';
  subjectModal.value.removingId = subjectId;
  try {
    const url = route('students.subjects.detach', { student: subjectModal.value.student.id, subject: subjectId });
    const res = await fetch(url, {
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken(),
      },
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      subjectModal.value.error = data.message || 'Imeshindikana kuondoa somo.';
      return;
    }
    applySubjectPayload(data);
    syncListAfterChange();
    window.__toast && window.__toast('success', 'Somo limeondolewa.');
  } catch (e) {
    subjectModal.value.error = e instanceof Error ? e.message : 'Imeshindikana kuondoa somo.';
  } finally {
    subjectModal.value.removingId = null;
  }
}

function onSearchKeydown(e) {
  if (!sug.value.open) return;
  if (e.key === 'ArrowDown') { e.preventDefault(); sug.value.index = Math.min(sug.value.index + 1, sug.value.items.length - 1); }
  if (e.key === 'ArrowUp') { e.preventDefault(); sug.value.index = Math.max(sug.value.index - 1, 0); }
  if (e.key === 'Enter' && sug.value.index >= 0) {
    e.preventDefault();
    const item = sug.value.items[sug.value.index];
    if (item) openDetails(item);
  }
  if (e.key === 'Escape') { sug.value.open = false; }
}

// Student details modal
const showDetails = ref(false);
const details = ref(null);
const detailsLoading = ref(false);
const detailsError = ref('');
const historyItems = ref([]);
const detailsTab = ref('profile');
// behaviours state
const behaviours = ref([]);
const bh = ref({ type: 'Merit', severity: '', date: new Date().toISOString().slice(0,10), description: '' });
const bhSaving = ref(false);
// status state
const st = ref({ status: 'active', status_reason: '', status_date: new Date().toISOString().slice(0,10) });
const stSaving = ref(false);
async function openDetails(item) {
  sug.value.open = false;
  detailsLoading.value = true;
  detailsError.value = '';
  showDetails.value = true;
  try {
    const url = route('students.show.json', item.id ? { id: item.id } : { reg_no: item.reg_no });
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (res.ok) {
      details.value = await res.json();
      // If class_info exists but class_subjects missing, fetch by class_id
      if ((!(details.value.class_subjects) || details.value.class_subjects.length===0) && details.value.class_info?.id) {
        try {
          const cres = await fetch(route('classes.show.json', { id: details.value.class_info.id }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          if (cres.ok) {
            const cdata = await cres.json();
            details.value.class_subjects = (cdata?.subjects) || [];
          }
        } catch (e) {}
      }
    } else {
      details.value = item; // fallback to provided data
      detailsError.value = 'Failed to load full details.';
      // Fallback: try to load class subjects directly by class name
      if (item?.class_name) {
        try {
          const cres = await fetch(route('classes.show.json', { name: item.class_name }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          if (cres.ok) {
            const cdata = await cres.json();
            details.value.class_subjects = (cdata?.subjects) || [];
          }
        } catch (e) {}
      }
    }
    // init status edit defaults
    st.value.status = details.value?.status || 'active';
    st.value.status_reason = details.value?.status_reason || '';
    st.value.status_date = details.value?.status_date || new Date().toISOString().slice(0,10);
    // load history (placeholder)
    const hres = await fetch(route('students.history', { id: details.value?.id || item.id }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    historyItems.value = hres.ok ? (await hres.json()).items : [];
    // load behaviours
    const bres = await fetch(route('students.behaviours', { id: details.value?.id || item.id }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    behaviours.value = bres.ok ? (await bres.json()).items : [];
  } finally {
    detailsLoading.value = false;
  }
}

async function saveBehaviour() {
  if (!details.value?.id) return;
  bhSaving.value = true;
  try {
    const resp = await fetch(route('students.behaviours.add', { id: details.value.id }), {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]')?.getAttribute('content')) || '' },
      body: new URLSearchParams({
        type: bh.value.type,
        severity: bh.value.severity || '',
        date: bh.value.date,
        description: bh.value.description || '',
      })
    });
    if (resp.ok) {
      // refresh list
      const bres = await fetch(route('students.behaviours', { id: details.value.id }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      behaviours.value = bres.ok ? (await bres.json()).items : behaviours.value;
      // reset description only
      bh.value.description = '';
      window.__toast && window.__toast('success', 'Behaviour saved');
    }
    else { window.__toast && window.__toast('error', 'Failed to save behaviour'); }
  } finally {
    bhSaving.value = false;
  }
}

async function saveStatus() {
  if (!details.value?.id) return;
  stSaving.value = true;
  try {
    const resp = await fetch(route('students.status.update', { id: details.value.id }), {
      method: 'PATCH',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]')?.getAttribute('content')) || '', 'Content-Type': 'application/json' },
      body: JSON.stringify({
        status: st.value.status,
        status_reason: st.value.status_reason || '',
        status_date: st.value.status_date,
      })
    });
    if (resp.ok) {
      details.value.status = st.value.status;
      details.value.status_reason = st.value.status_reason;
      details.value.status_date = st.value.status_date;
      window.__toast && window.__toast('success', 'Status updated');
    }
    else { window.__toast && window.__toast('error', 'Failed to update status'); }
  } finally {
    stSaving.value = false;
  }
}

// Import CSV modal state
const showImport = ref(false);
const importFile = ref(null);
const importing = ref(false);
const preview = ref({ headers: [], rows: [], total: 0 });
function openImport() {
  preview.value = { headers: [], rows: [], total: 0 };
  importFile.value = null;
  showImport.value = true;
}
function parseCsv(text) {
  const lines = text.split(/\r?\n/).filter(l => l.trim().length);
  if (!lines.length) return { headers: [], rows: [], total: 0 };
  const headers = lines[0].split(',').map(h => h.trim());
  const rows = lines.slice(1).map(l => l.split(',').map(c => c.trim()));
  return { headers, rows: rows.slice(0, 10), total: rows.length };
}
function onPickImport(e) {
  const file = e.target.files?.[0];
  importFile.value = file || null;
  if (!file) { preview.value = { headers: [], rows: [], total: 0 }; return; }
  const reader = new FileReader();
  reader.onload = () => {
    try {
      preview.value = parseCsv(String(reader.result || ''));
    } catch (e) {
      preview.value = { headers: [], rows: [], total: 0 };
    }
  };
  reader.readAsText(file);
}
async function confirmImport() {
  if (!importFile.value) return;
  const fd = new FormData();
  fd.append('file', importFile.value);
  importing.value = true;
  try {
    await router.post(route('students.import'), fd, {
      preserveScroll: true,
      onSuccess: () => { showImport.value = false; window.__toast && window.__toast('success', 'Import completed'); },
      onError: () => { window.__toast && window.__toast('error', 'Import failed'); }
    });
  } finally {
    importing.value = false;
  }
}

// Add Student modal state
const showAdd = ref(false);
const form = ref({
  name: '',
  sex: 'M',
  class_name: '',
  dob: '',
  academic_year: new Date().getFullYear(),
  guardian_name: '',
  guardian_phone: '',
  guardian_relation: '',
  exam_no: '',
  photo: null,
});
const prefix = computed(() => props.school_number ? `${props.school_number}-` : '');
function onPhoto(e) { form.value.photo = e.target.files?.[0] ?? null; }
function ensureExamPrefix() {
  if (!prefix.value) return;
  const v = form.value.exam_no || '';
  if (v && !v.startsWith(prefix.value)) {
    form.value.exam_no = prefix.value + v.replace(/^.*?-/, '');
  }
}
const submitting = ref(false);
async function submitAdd() {
  const fd = new FormData();
  Object.entries(form.value).forEach(([k, v]) => {
    if (v !== null && v !== undefined && v !== '') fd.append(k, v);
  });
  submitting.value = true;
  try {
    await router.post(route('students.store'), fd, {
      preserveScroll: true,
      onSuccess: () => { showAdd.value = false; window.__toast && window.__toast('success', 'Student added successfully'); },
      onError: (errs) => { const msg = Object.values(errs||{})[0] || 'Failed to add student'; window.__toast && window.__toast('error', String(msg)); },
    });
    // reset minimal fields
    form.value = { name: '', sex: 'M', class_name: '', dob: '', academic_year: new Date().getFullYear(), guardian_name: '', guardian_phone: '', guardian_relation: '', exam_no: prefix.value, photo: null };
  } finally {
    submitting.value = false;
  }
}

// Server validation errors
const page = usePage();
const errors = computed(() => page.props.errors || {});
const flash = computed(() => page.props.flash || {});

// Group by class toggle and chips
const groupByClass = ref(false);
const classCounts = computed(() => {
  const counts = {};
  (props.students?.data || []).forEach(s => { counts[s.class_name] = (counts[s.class_name] || 0) + 1; });
  return counts;
});
const classChips = computed(() => Object.keys(classCounts.value).sort());

// Unified class options (prefer DB classes for accuracy)
const filterClassOptions = computed(() => {
  const set = new Set([...(props.all_classes || []), ...(props.classes || [])]);
  return Array.from(set).filter(Boolean).sort();
});
const addClassOptions = computed(() => (props.all_classes && props.all_classes.length ? props.all_classes : props.classes));

// Guardian stats (live) based on phone
const guardianStats = ref({ count: 0, students: [] });
let phoneDebounce;
watch(() => form.value.guardian_phone, (val) => {
  if (phoneDebounce) clearTimeout(phoneDebounce);
  phoneDebounce = setTimeout(async () => {
    if (!val) { guardianStats.value = { count: 0, students: [] }; return; }
    try {
      const url = route('students.guardian.stats', { phone: val });
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (res.ok) {
        guardianStats.value = await res.json();
      }
    } catch (e) {}
  }, 300);
});
</script>

<template>
  <Head title="Students" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Students</h2>
    </template>

    <div class="space-y-4">
      <!-- Filters -->
      <div class="flex flex-col gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:flex-row sm:items-end sm:justify-between">
        <div class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-3">
          <div class="relative">
            <label for="q" class="mb-1 block text-xs font-medium text-gray-600">Search (Reg_No or Name)</label>
            <input id="q" v-model="q" @keydown="onSearchKeydown" @keyup.enter="submitFilters()" type="text" placeholder="e.g. RSMS-001 or Asha" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
            <div v-if="sug.open" class="absolute z-20 mt-1 w-full overflow-hidden rounded-md border border-gray-200 bg-white shadow">
              <div v-if="sug.loading" class="p-2 text-xs text-gray-500">Loading...</div>
              <ul v-else class="max-h-60 overflow-auto text-sm">
                <li v-for="(it, i) in sug.items" :key="it.reg_no"
                    @click="openDetails(it)"
                    :class="['cursor-pointer px-3 py-2 hover:bg-gray-50', i===sug.index ? 'bg-gray-50' : '']">
                  <div class="flex items-center justify-between">
                    <div class="font-medium text-gray-800">{{ it.name }}</div>
                    <div class="text-xs text-gray-500">{{ it.reg_no }}</div>
                  </div>

      <!-- Flash Toast -->
      <div v-if="flash.success || flash.error" class="pointer-events-none fixed right-4 top-4 z-50 space-y-2">
        <div v-if="flash.success" class="pointer-events-auto rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow">{{ flash.success }}</div>
        <div v-if="flash.error" class="pointer-events-auto rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow">{{ flash.error }}</div>
      </div>

      <!-- Delete Confirm Modal -->
      <div v-if="showDelete" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showDelete = false"></div>
        <div class="relative z-10 w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl">
          <div class="flex items-center justify-between border-b px-5 py-3">
            <h3 class="text-base font-semibold text-gray-800">Delete Student</h3>
            <button @click="showDelete = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div class="space-y-3 p-5 text-sm">
            <p class="text-gray-700">Are you sure you want to delete <span class="font-semibold">{{ del.name }}</span> ({{ del.reg_no }})? This action cannot be undone.</p>
          </div>
          <div class="flex items-center justify-end gap-2 border-t px-5 py-3">
            <button @click="showDelete = false" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
            <button :disabled="deleting" @click="confirmDelete" type="button" class="rounded-md bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 disabled:opacity-60">{{ deleting ? 'Deleting...' : 'Delete' }}</button>
          </div>
        </div>
      </div>
                  <div class="text-xs text-gray-500">{{ it.class_name }} • {{ it.sex }}</div>
                </li>
              </ul>
            </div>
          </div>

      <!-- Student Details Modal -->
      <div v-if="showDetails" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showDetails = false"></div>
        <div class="relative z-10 w-full max-w-4xl overflow-hidden rounded-xl bg-white shadow-xl">
          <div class="flex items-center justify-between border-b px-5 py-3">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 overflow-hidden rounded-full bg-gray-100">
                <img v-if="details?.photo_path" :src="'/storage/' + details.photo_path" alt="photo" class="h-10 w-10 object-cover" />
              </div>
              <div>
                <div class="text-base font-semibold text-gray-800">{{ details?.name || 'Student' }}</div>
                <div class="text-xs text-gray-500">{{ details?.reg_no }} • {{ details?.class_name }} • {{ details?.sex }}</div>
              </div>
            </div>
            <button @click="showDetails = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div class="px-5 pt-4">
            <div v-if="detailsError" class="mx-5 mb-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
              {{ detailsError }} <button @click="openDetails(details || {})" class="ml-2 rounded border border-amber-300 px-2 py-0.5 text-amber-700 hover:bg-amber-100">Retry</button>
            </div>
            <div class="flex flex-wrap gap-2 border-b pb-2 text-sm">
              <button @click="detailsTab = 'profile'" :class="detailsTab==='profile' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-600'" class="px-2 py-1">Profile</button>
              <button @click="detailsTab = 'behaviours'" :class="detailsTab==='behaviours' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-600'" class="px-2 py-1">Behaviours</button>
              <button @click="detailsTab = 'results'" :class="detailsTab==='results' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-600'" class="px-2 py-1">Results History</button>
              <button @click="detailsTab = 'status'" :class="detailsTab==='status' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-600'" class="px-2 py-1">Status & Actions</button>
            </div>
          </div>
          <div class="p-5">
            <!-- Profile Tab -->
            <div v-if="detailsTab==='profile'" class="grid gap-4 sm:grid-cols-2">
              <div class="rounded-lg border border-gray-100 p-4">
                <div class="text-sm font-semibold text-gray-800">Registration</div>
                <div class="mt-2 grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
                  <div>
                    <div class="text-xs text-gray-500">Reg No</div>
                    <div class="mt-0.5 inline-flex items-center rounded border border-gray-200 bg-gray-50 px-2 py-0.5 text-gray-800">{{ details?.reg_no }}</div>
                  </div>
                  <div>
                    <div class="text-xs text-gray-500">Class</div>
                    <div class="mt-0.5 inline-flex items-center rounded border border-gray-200 bg-gray-50 px-2 py-0.5 text-gray-800">{{ details?.class_name }}</div>
                  </div>
                  <div>
                    <div class="text-xs text-gray-500">Sex</div>
                    <div class="mt-0.5 inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ details?.sex }}</div>
                  </div>
                  <div>
                    <div class="text-xs text-gray-500">DOB</div>
                    <div class="mt-0.5 inline-flex items-center rounded border border-gray-200 bg-gray-50 px-2 py-0.5 text-gray-800">{{ details?.dob || '-' }}</div>
                  </div>
                  <div class="sm:col-span-2">
                    <div class="text-xs text-gray-500">Exam No</div>
                    <div class="mt-0.5 inline-flex items-center rounded border border-gray-200 bg-gray-50 px-2 py-0.5 text-gray-800">{{ details?.exam_no || '-' }}</div>
                  </div>
                </div>
                <div class="mt-3 flex items-center justify-between">
                  <div class="text-sm font-semibold text-gray-800">Assigned subjects</div>
                  <a v-if="details?.class_info?.id" :href="route('classes.show', { id: details.class_info.id })" class="text-xs text-green-700 hover:underline">Open class</a>
                </div>
                <div class="mt-1 flex flex-wrap gap-1">
                  <span v-for="sub in (details?.class_subjects || [])" :key="sub.id" :title="sub.name"
                        class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                    {{ sub.code || sub.name }}
                  </span>
                  <span v-if="!details?.class_subjects || details.class_subjects.length===0" class="text-xs text-gray-500">No subjects</span>
                </div>
                <div v-if="details?.class_subjects && details.class_subjects.length" class="mt-3 overflow-hidden rounded-md border border-gray-100">
                  <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Code</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Name</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                      <tr v-for="sub in details.class_subjects" :key="sub.id" class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-gray-900">{{ sub.code || '—' }}</td>
                        <td class="px-3 py-2 text-gray-700">{{ sub.name }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="rounded-lg border border-gray-100 p-4">
                <div class="text-sm font-semibold text-gray-800">Guardian</div>
                <div class="mt-2 text-sm text-gray-700">Name: {{ details?.guardian_name || '-' }}</div>
                <div class="text-sm text-gray-700">Phone: {{ details?.guardian_phone || '-' }}</div>
                <div class="text-sm text-gray-700">Relation: {{ details?.guardian_relation || '-' }}</div>
              </div>
            </div>

            <!-- Behaviours Tab -->
            <div v-if="detailsTab==='behaviours'" class="space-y-4">
              <div class="rounded-lg border border-gray-100 p-4">
                <div class="mb-3 text-sm font-semibold text-gray-800">Add Behaviour</div>
                <div class="grid gap-3 sm:grid-cols-4">
                  <select v-model="bh.type" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                    <option value="Merit">Merit</option>
                    <option value="Demerit">Demerit</option>
                  </select>
                  <select v-model="bh.severity" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                    <option value="">Severity</option>
                    <option>Low</option>
                    <option>Medium</option>
                    <option>High</option>
                  </select>
                  <input v-model="bh.date" type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
                  <button :disabled="bhSaving" @click="saveBehaviour" class="rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ bhSaving ? 'Saving...' : 'Save' }}</button>
                </div>
                <textarea v-model="bh.description" rows="3" placeholder="Description (optional)" class="mt-3 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600"></textarea>
              </div>
              <div class="rounded-lg border border-gray-100">
                <div class="border-b px-4 py-2 text-sm font-semibold text-gray-800">Recent Behaviours</div>
                <div class="max-h-64 overflow-auto p-2">
                  <div v-if="behaviours.length===0" class="p-3 text-xs text-gray-500">No behaviour records.</div>
                  <ul class="divide-y divide-gray-100 text-sm">
                    <li v-for="it in behaviours" :key="it.id" class="flex items-start justify-between gap-3 px-3 py-2">
                      <div>
                        <div class="font-medium text-gray-800">{{ it.type }} <span class="ml-2 rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600" v-if="it.severity">{{ it.severity }}</span></div>
                        <div class="text-xs text-gray-600">{{ it.date || '-' }}</div>
                        <div class="text-xs text-gray-700">{{ it.description }}</div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Results Tab (placeholder for now) -->
            <div v-if="detailsTab==='results'" class="rounded-lg border border-gray-100 p-4 text-sm text-gray-600">
              Results history integration can be wired here.
            </div>

            <!-- Status Tab -->
            <div v-if="detailsTab==='status'" class="space-y-4">
              <div class="rounded-lg border border-gray-100 p-4">
                <div class="mb-3 text-sm font-semibold text-gray-800">Current Status</div>
                <div class="text-sm text-gray-700">Status: <span class="font-medium">{{ details?.status || 'active' }}</span></div>
                <div class="text-sm text-gray-700">Since: {{ details?.status_date || '-' }}</div>
                <div class="text-sm text-gray-700">Reason: {{ details?.status_reason || '-' }}</div>
              </div>
              <div class="rounded-lg border border-gray-100 p-4">
                <div class="mb-3 text-sm font-semibold text-gray-800">Update Status</div>
                <div class="grid gap-3 sm:grid-cols-3">
                  <select v-model="st.status" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="transferred">Transferred</option>
                    <option value="alumni">Alumni</option>
                    <option value="expelled">Expelled</option>
                  </select>
                  <input v-model="st.status_date" type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
                  <button :disabled="stSaving" @click="saveStatus" class="rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ stSaving ? 'Saving...' : 'Save' }}</button>
                </div>
                <textarea v-model="st.status_reason" rows="3" placeholder="Reason (optional)" class="mt-3 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600"></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Export Modal -->
      <div v-if="showExport" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showExport = false"></div>
        <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-xl">
          <div class="flex items-center justify-between border-b px-5 py-3">
            <h3 class="text-base font-semibold text-gray-800">Export Students</h3>
            <button @click="showExport = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div class="grid gap-4 p-5">
            <div>
              <div class="mb-1 text-xs font-medium text-gray-600">Scope</div>
              <div class="grid gap-2 sm:grid-cols-3">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                  <input type="radio" value="current" v-model="exp.scope" /> Current filters
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                  <input type="radio" value="all" v-model="exp.scope" /> All students
                </label>
                <div class="flex items-center gap-2">
                  <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="radio" value="class" v-model="exp.scope" /> Class
                  </label>
                  <select :disabled="exp.scope!=='class'" v-model="exp.class_name" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                    <option value="">Select class</option>
                    <option v-for="c in classes" :key="c" :value="c">{{ c }}</option>
                  </select>
                </div>
              </div>
            </div>
            <div>
              <div class="mb-1 text-xs font-medium text-gray-600">Format</div>
              <div class="flex items-center gap-4 text-sm text-gray-700">
                <label class="flex items-center gap-2"><input type="radio" value="csv" v-model="exp.format" /> CSV</label>
                <label class="flex items-center gap-2 opacity-70"><input type="radio" value="pdf" v-model="exp.format" /> PDF</label>
              </div>
              <p class="mt-1 text-xs text-gray-500">PDF will be enabled after installing the PDF package.</p>
            </div>
          </div>
          <div class="flex items-center justify-end gap-2 border-t px-5 py-3">
            <button @click="showExport = false" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
            <button @click="confirmExport" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">Export</button>
          </div>
        </div>
      </div>

      <!-- Import CSV Modal -->
      <div v-if="showImport" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showImport = false"></div>
        <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-xl bg-white shadow-xl">
          <div class="flex items-center justify-between border-b px-5 py-3">
            <h3 class="text-base font-semibold text-gray-800">Import Students (CSV)</h3>
            <button @click="showImport = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div class="grid gap-5 p-5 sm:grid-cols-2">
            <div class="space-y-3">
              <div class="rounded-md border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700">
                <div class="font-semibold text-gray-800">Instructions</div>
                <ol class="mt-2 list-decimal space-y-1 pl-5">
                  <li>Download the CSV template and fill it.</li>
                  <li>Required columns: <code>reg_no,name,sex,class_name</code>.</li>
                  <li>Optional columns: <code>guardian_name,guardian_phone,guardian_relation,academic_year,dob,exam_no</code>.</li>
                  <li>If you include <code>exam_no</code>, prefix must match your school number ({{ school_number }}-XXXX).</li>
                </ol>
              </div>
              <a :href="route('students.template')" class="inline-flex items-center justify-center rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Download Template</a>
              <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">Choose CSV file <span class="text-red-600">*</span></label>
                <input type="file" accept=".csv,text/csv" @change="onPickImport" class="w-full rounded-md border border-gray-300 text-sm shadow-sm" />
                <p class="mt-1 text-xs text-gray-500">Max size ~10MB. Use commas, no embedded commas inside values.</p>
              </div>
            </div>
            <div class="min-h-[200px]">
              <div class="mb-2 text-sm font-semibold text-gray-800">Preview <span v-if="preview.total" class="text-gray-500">(showing first 10 of {{ preview.total }})</span></div>
              <div v-if="preview.headers.length" class="overflow-x-auto rounded-md border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                  <thead class="bg-gray-50">
                    <tr>
                      <th v-for="h in preview.headers" :key="h" class="px-3 py-2 text-left font-medium text-gray-700">{{ h }}</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <tr v-for="(r, i) in preview.rows" :key="i">
                      <td v-for="(c, j) in r" :key="j" class="px-3 py-2 text-gray-700">{{ c }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="rounded-md border border-dashed border-gray-300 p-6 text-center text-xs text-gray-500">No file selected.</div>
            </div>
          </div>
          <div class="flex items-center justify-end gap-2 border-t px-5 py-3">
            <button @click="showImport = false" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
            <button :disabled="!importFile || importing" @click="confirmImport" type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60">{{ importing ? 'Importing...' : 'Confirm Import' }}</button>
          </div>
        </div>
      </div>
          <div>
            <label for="class" class="mb-1 block text-xs font-medium text-gray-600">Class</label>
            <select id="class" v-model="fClass" @change="submitFilters()" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option value="">All</option>
              <option v-for="c in filterClassOptions" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label for="sex" class="mb-1 block text-xs font-medium text-gray-600">Sex</label>
            <select id="sex" v-model="fSex" @change="submitFilters()" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option value="">All</option>
              <option value="M">Male</option>
              <option value="F">Female</option>
            </select>
          </div>
        </div>
        <div class="flex gap-2">
          <button type="button" @click="clearFilters()" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Clear</button>
          <button type="button" @click="submitFilters()" class="rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700">Search</button>
        </div>
      </div>

      <!-- Import / Export -->
      <div class="flex flex-col gap-2 rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-gray-600">Bulk actions</div>
        <div class="flex flex-wrap items-center gap-2">
          <button type="button" @click="openImport()" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">Import CSV</button>
          <a :href="route('students.template')" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Download Template</a>
          <button type="button" @click="openExport()" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Export</button>
          <button type="button" @click="openAdd()" class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700">Add Student</button>
        </div>
      </div>

      <!-- Class chips and grouping toggle -->
      <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <div class="flex flex-wrap gap-2">
            <button type="button" @click="fClass=''; submitFilters()" :class="['rounded-full px-3 py-1 text-xs', fClass==='' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">All</button>
            <button v-for="c in filterClassOptions" :key="c" type="button" @click="fClass=c; submitFilters()" :class="['rounded-full px-3 py-1 text-xs', fClass===c ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">
              {{ c }}<span class="ml-1 text-[10px] text-gray-500" v-if="classCounts[c]"> ({{ classCounts[c] }})</span>
            </button>
          </div>
          <label class="flex items-center gap-2 text-xs text-gray-700">
            <input type="checkbox" v-model="groupByClass" /> Group by class
          </label>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table v-if="!groupByClass" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Reg_No</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Full name</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Sex</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Class</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Subjects</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-700">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="s in students.data" :key="s.reg_no" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-900">{{ s.reg_no }}</td>
                <td class="px-4 py-3 text-gray-700">{{ s.name }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ s.sex }}</span>
                </td>
                <td class="px-4 py-3 text-gray-700">{{ s.class_name }}</td>
                <td class="px-4 py-3 text-xs text-gray-700">
                  <div v-if="classSubjectsForStudent(s).length || assignedOptionalSubjectsForStudent(s).length" class="space-y-2">
                    <div v-if="classCoreSubjectsForStudent(s).length">
                      <div class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">Core</div>
                      <div class="flex flex-wrap gap-1">
                        <span v-for="sub in classCoreSubjectsForStudent(s)" :key="`core-${s.reg_no}-${sub.id}`" class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700">
                          <span>{{ subjectLabel(sub) }}</span>
                        </span>
                      </div>
                    </div>
                    <div v-if="classElectiveSubjectsForStudent(s).length">
                      <div class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-amber-600">Class Optional</div>
                      <div class="flex flex-wrap gap-1">
                        <span v-for="sub in classElectiveSubjectsForStudent(s)" :key="`cls-opt-${s.reg_no}-${sub.id}`" class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700">
                          <span>{{ subjectLabel(sub) }}</span>
                        </span>
                      </div>
                    </div>
                    <div v-if="assignedOptionalSubjectsForStudent(s).length">
                      <div class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-emerald-600">Optional</div>
                      <div class="flex flex-wrap gap-1">
                        <span v-for="sub in assignedOptionalSubjectsForStudent(s)" :key="`opt-${s.reg_no}-${sub.id}`" class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700">
                          <span>{{ subjectLabel(sub) }}</span>
                          <button
                            type="button"
                            @click.stop="removeInlineSubject(s, sub)"
                            :disabled="isInlineRemoving(s.id, sub.id)"
                            class="flex h-4 w-4 items-center justify-center rounded-full bg-emerald-200/60 text-[10px] text-emerald-900 hover:bg-emerald-300 disabled:opacity-50"
                            aria-label="Remove subject"
                          >
                            <svg v-if="isInlineRemoving(s.id, sub.id)" class="h-3 w-3 animate-spin text-emerald-900" viewBox="0 0 24 24" fill="none">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span v-else>×</span>
                          </button>
                        </span>
                      </div>
                    </div>
                    <div v-if="inlineRemovedFor(s.id).length" class="space-y-1 border-t border-dashed border-emerald-200 pt-2">
                      <div class="text-[11px] font-semibold uppercase tracking-wide text-emerald-500">Removed (click to add back)</div>
                      <div class="flex flex-wrap gap-1">
                        <button
                          v-for="sub in inlineRemovedFor(s.id)"
                          :key="`removed-${s.reg_no}-${sub.id}`"
                          type="button"
                          @click.stop="addInlineSubject(s, sub)"
                          :disabled="isInlineAdding(s.id, sub.id)"
                          class="inline-flex items-center gap-2 rounded-full border border-emerald-300 bg-white px-2 py-0.5 text-[11px] font-medium text-emerald-600 hover:bg-emerald-50 disabled:opacity-60"
                        >
                          <span class="rounded-full bg-emerald-100 px-1.5 py-0.5 text-[9px] uppercase tracking-wide text-emerald-600">Add</span>
                          <span class="flex items-center gap-1">
                            <svg v-if="isInlineAdding(s.id, sub.id)" class="h-3 w-3 animate-spin text-emerald-600" viewBox="0 0 24 24" fill="none">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span>{{ subjectName(sub) }}</span>
                          </span>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-[11px] italic text-gray-400">No subjects yet</div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <button type="button" @click.stop="openSubjects(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-200 hover:bg-amber-100" aria-label="Manage Subjects">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 006.5 22H20"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 014 17.5V4.5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h8M8 10h8M8 14h5"/></svg>
                    </button>
                    <!-- View -->
                    <button type="button" @click.stop="openDetails(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-100" aria-label="View">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/></svg>
                    </button>
                    <!-- History -->
                    <button type="button" @click.stop="openDetails(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-50 text-sky-600 ring-1 ring-inset ring-sky-200 hover:bg-sky-100" aria-label="History">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 109-9v2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v6h6"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/></svg>
                    </button>
                    <!-- Edit -->
                    <button type="button" @click.stop="openEdit(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100" aria-label="Edit">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 113 3L8.25 18.1 4.5 19.5l1.4-3.75 10.962-12.263z"/></svg>
                    </button>
                    <!-- Delete -->
                    <button type="button" @click.stop="openDelete(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200 hover:bg-rose-100" aria-label="Delete">
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-1 0l-.867 12.142A2 2 0 0114.138 21H9.862a2 2 0 01-1.995-1.858L7 7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="(students.data || []).length === 0">
                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No students found.</td>
              </tr>
            </tbody>
          </table>
          <!-- Grouped rendering -->
          <div v-else class="divide-y divide-gray-100">
            <div v-for="(count, cls) in classCounts" :key="cls" class="p-0">
              <div class="sticky top-0 z-10 bg-white px-4 py-2 text-sm font-semibold text-gray-800 ring-1 ring-gray-100">{{ cls }} <span class="ml-1 text-xs text-gray-500">({{ count }})</span></div>
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Reg_No</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Full name</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Sex</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Subjects</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="s in (students.data || []).filter(x => x.class_name===cls)" :key="s.reg_no" class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-900">{{ s.reg_no }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ s.name }}</td>
                    <td class="px-4 py-3">
                      <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ s.sex }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700">
                      <div v-if="classSubjectsForStudent(s).length || assignedOptionalSubjectsForStudent(s).length" class="space-y-2">
                        <div v-if="classCoreSubjectsForStudent(s).length">
                          <div class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">Core</div>
                          <div class="flex flex-wrap gap-1">
                            <span v-for="sub in classCoreSubjectsForStudent(s)" :key="`group-core-${s.reg_no}-${sub.id}`" class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700">
                              {{ subjectLabel(sub) }}
                            </span>
                          </div>
                        </div>
                        <div v-if="classElectiveSubjectsForStudent(s).length">
                          <div class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-amber-600">Class Optional</div>
                          <div class="flex flex-wrap gap-1">
                            <span v-for="sub in classElectiveSubjectsForStudent(s)" :key="`group-cls-opt-${s.reg_no}-${sub.id}`" class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700">
                              <span>{{ subjectLabel(sub) }}</span>
                            </span>
                          </div>
                        </div>
                        <div v-if="assignedOptionalSubjectsForStudent(s).length">
                          <div class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-emerald-600">Optional</div>
                          <div class="flex flex-wrap gap-1">
                            <span v-for="sub in assignedOptionalSubjectsForStudent(s)" :key="`group-opt-${s.reg_no}-${sub.id}`" class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700">
                              <span>{{ subjectLabel(sub) }}</span>
                              <button type="button" @click.stop="removeInlineSubject(s, sub)" :disabled="isInlineRemoving(s.id, sub.id)" class="rounded-full bg-emerald-200/60 p-0.5 text-[10px] text-emerald-900 hover:bg-emerald-300 disabled:opacity-50">
                                ×
                              </button>
                            </span>
                          </div>
                        </div>
                        <div v-if="inlineRemovedFor(s.id).length" class="space-y-1 border-t border-dashed border-emerald-200 pt-2">
                          <div class="text-[11px] font-semibold uppercase tracking-wide text-emerald-500">Removed (click to add back)</div>
                          <div class="flex flex-wrap gap-1">
                            <button
                              v-for="sub in inlineRemovedFor(s.id)"
                              :key="`group-removed-${s.reg_no}-${sub.id}`"
                              type="button"
                              @click.stop="addInlineSubject(s, sub)"
                              :disabled="isInlineAdding(s.id, sub.id)"
                              class="inline-flex items-center gap-2 rounded-full border border-emerald-300 bg-white px-2 py-0.5 text-[11px] font-medium text-emerald-600 hover:bg-emerald-50 disabled:opacity-60"
                            >
                              <span class="rounded-full bg-emerald-100 px-1.5 py-0.5 text-[9px] uppercase tracking-wide text-emerald-600">Add</span>
                              <span class="flex items-center gap-1">
                                <svg v-if="isInlineAdding(s.id, sub.id)" class="h-3 w-3 animate-spin text-emerald-600" viewBox="0 0 24 24" fill="none">
                                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                </svg>
                                <span>{{ subjectName(sub) }}</span>
                              </span>
                            </button>
                          </div>
                        </div>
                      </div>
                      <div v-else class="text-[11px] italic text-gray-400">No subjects yet</div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex justify-end gap-2">
                        <button type="button" @click.stop="openSubjects(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-200 hover:bg-amber-100" aria-label="Manage Subjects">
                          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 006.5 22H20"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 014 17.5V4.5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h8M8 10h8M8 14h5"/></svg>
                        </button>
                        <button type="button" @click.stop="openDetails(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-100" aria-label="View">
                          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/></svg>
                        </button>
                        <button type="button" @click.stop="openEdit(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100" aria-label="Edit">
                          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 113 3L8.25 18.1 4.5 19.5l1.4-3.75 10.962-12.263z"/></svg>
                        </button>
                        <button type="button" @click.stop="openDelete(s)" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200 hover:bg-rose-100" aria-label="Delete">
                          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-1 0l-.867 12.142A2 2 0 0114.138 21H9.862a2 2 0 01-1.995-1.858L7 7z"/></svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="students.links" class="flex flex-wrap items-center justify-between gap-2">
        <div class="text-xs text-gray-500">Showing {{ students.from || 0 }}-{{ students.to || 0 }} of {{ students.total || 0 }}</div>
        <div class="flex flex-wrap gap-1">
          <button
            v-for="(link, i) in students.links"
            :key="i"
            :disabled="!link.url"
            @click="link.url && submitFilters(link.url)"
            class="rounded-md px-3 py-1.5 text-sm"
            :class="[
              link.active ? 'bg-green-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50',
              !link.url ? 'opacity-50' : ''
            ]"
            v-html="link.label"
          />
        </div>
      </div>

      <!-- Subjects Modal -->
      <div v-if="subjectModal.open" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="closeSubjects"></div>
        <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-xl bg-white shadow-xl">
          <div class="flex items-center justify-between border-b px-5 py-3">
            <div>
              <div class="text-base font-semibold text-gray-800">Manage Subjects</div>
              <div class="text-xs text-gray-500">
                {{ subjectModal.student?.name }} • {{ subjectModal.student?.reg_no }} • {{ subjectModal.student?.class_name || 'No class' }}
              </div>
            </div>
            <button @click="closeSubjects" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div class="grid gap-5 p-5 md:grid-cols-2">
            <div>
              <div class="mb-2 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Class Core Subjects</h3>
                <span v-if="subjectModal.loading" class="text-xs text-gray-500">Loading…</span>
              </div>
              <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-xs text-gray-700">
                <div v-if="subjectModal.core.length" class="flex flex-wrap gap-1">
                  <span v-for="sub in subjectModal.core" :key="`modal-core-${sub.id}`" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[11px] font-medium text-slate-700">
                    <span class="mr-1 rounded-full bg-slate-100 px-1.5 py-0.5 text-[10px] uppercase tracking-wide text-slate-500">CORE</span>
                    {{ subjectCode(sub) }}
                  </span>
                </div>
                <div v-else class="text-[11px] italic text-gray-400">No core subjects detected for this class.</div>
              </div>
            </div>
            <div class="space-y-4">
              <div class="rounded-lg border border-gray-100 p-3">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                  <h3 class="text-sm font-semibold text-gray-800">Add Optional Subject</h3>
                  <div v-if="subjectModal.error" class="text-xs text-rose-600">{{ subjectModal.error }}</div>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                  <select v-model="subjectModal.addId" :disabled="subjectModal.saving || subjectModal.loading" class="flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <option value="">Chagua somo</option>
                    <option v-for="sub in subjectModal.available" :key="sub.id" :value="String(sub.id)">
                      {{ subjectCode(sub) }} — {{ sub.name }}
                    </option>
                  </select>
                  <button type="button" @click="addOptionalSubject" :disabled="subjectModal.saving || subjectModal.loading" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-60">
                    {{ subjectModal.saving ? 'Saving…' : 'Add Subject' }}
                  </button>
                </div>
                <p v-if="!subjectModal.available.length" class="mt-2 text-xs text-gray-500">Hakuna masomo ya ziada yaliyobaki. Jaribu kuondoa ili kubadilisha.</p>
              </div>
              <div class="rounded-lg border border-gray-100">
                <div class="border-b px-4 py-2 text-sm font-semibold text-gray-800">Optional Subjects</div>
                <div v-if="subjectModal.optional.length" class="max-h-64 overflow-auto divide-y divide-gray-100 text-sm">
                  <div v-for="sub in subjectModal.optional" :key="`modal-opt-${sub.id}`" class="flex items-center justify-between gap-3 px-4 py-2">
                    <div>
                      <div class="font-medium text-gray-800">{{ subjectCode(sub) }}</div>
                      <div class="text-xs text-gray-500">{{ sub.name }}</div>
                    </div>
                    <button type="button" @click="removeOptionalSubject(sub.id)" :disabled="subjectModal.removingId === sub.id" class="inline-flex items-center justify-center rounded-md border border-rose-200 bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 hover:bg-rose-100 disabled:opacity-60">
                      {{ subjectModal.removingId === sub.id ? 'Removing…' : 'Remove' }}
                    </button>
                  </div>
                </div>
                <div v-else class="p-4 text-xs text-gray-500">Haujachagua somo la ziada bado.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Student Modal -->
      <div v-if="showAdd" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showAdd = false"></div>
        <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-xl">
          <div class="flex items-center justify-between border-b px-5 py-3">
            <h3 class="text-base font-semibold text-gray-800">Add Student</h3>
            <button @click="showAdd = false" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div class="grid gap-4 p-5 sm:grid-cols-2">
            <!-- Student info -->
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Full Name <span class="text-red-600">*</span></label>
              <input v-model="form.name" required type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Sex <span class="text-red-600">*</span></label>
              <select v-model="form.sex" required class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                <option value="M">Male</option>
                <option value="F">Female</option>
              </select>
              <p v-if="errors.sex" class="mt-1 text-xs text-red-600">{{ errors.sex }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Class <span class="text-red-600">*</span></label>
              <select v-model="form.class_name" required class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                <option value="" disabled>Select class</option>
                <option v-for="c in addClassOptions" :key="c" :value="c">{{ c }}</option>
              </select>
              <p v-if="errors.class_name" class="mt-1 text-xs text-red-600">{{ errors.class_name }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Date of Birth <span class="text-red-600">*</span></label>
              <input v-model="form.dob" required type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              <p v-if="errors.dob" class="mt-1 text-xs text-red-600">{{ errors.dob }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Academic Year <span class="text-red-600">*</span></label>
              <input v-model="form.academic_year" required type="number" min="2000" max="2100" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              <p v-if="errors.academic_year" class="mt-1 text-xs text-red-600">{{ errors.academic_year }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Exam Number <span class="text-red-600">*</span></label>
              <div class="flex">
                <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-2 text-xs text-gray-600">{{ prefix }}</span>
                <input v-model="form.exam_no" required @blur="ensureExamPrefix" type="text" placeholder="e.g. 0011" class="w-full rounded-r-md border border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              </div>
              <p class="mt-1 text-xs text-gray-500" v-if="prefix">Format: {{ prefix }}XXXX (e.g. {{ prefix }}0011)</p>
              <p v-if="errors.exam_no" class="mt-1 text-xs text-red-600">{{ errors.exam_no }}</p>
            </div>
            <div class="sm:col-span-2">
              <label class="mb-1 block text-xs font-medium text-gray-600">Photo</label>
              <input type="file" accept="image/*" @change="onPhoto" class="w-full rounded-md border border-gray-300 text-sm shadow-sm" />
            </div>

            <!-- Guardian -->
            <div class="sm:col-span-2 mt-2">
              <div class="text-sm font-semibold text-gray-800">Guardian Details</div>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Guardian Name <span class="text-red-600">*</span></label>
              <input v-model="form.guardian_name" required type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              <p v-if="errors.guardian_name" class="mt-1 text-xs text-red-600">{{ errors.guardian_name }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600">Relation <span class="text-red-600">*</span></label>
              <select v-model="form.guardian_relation" required class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                <option value="">Select</option>
                <option value="Parent">Parent</option>
                <option value="Mother">Mother</option>
                <option value="Father">Father</option>
                <option value="Guardian">Guardian</option>
                <option value="Relative">Relative</option>
              </select>
              <p v-if="errors.guardian_relation" class="mt-1 text-xs text-red-600">{{ errors.guardian_relation }}</p>
            </div>
            <div class="sm:col-span-2">
              <label class="mb-1 block text-xs font-medium text-gray-600">Guardian Phone <span class="text-red-600">*</span></label>
              <input v-model="form.guardian_phone" required type="tel" inputmode="tel" placeholder="2557########" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
              <p v-if="errors.guardian_phone" class="mt-1 text-xs text-red-600">{{ errors.guardian_phone }}</p>
              <div v-if="guardianStats.count>0" class="mt-2 rounded-md border border-amber-200 bg-amber-50 p-2 text-xs text-amber-800">
                This guardian has {{ guardianStats.count }} student(s):
                <ul class="mt-1 list-disc pl-5">
                  <li v-for="st in guardianStats.students" :key="st.reg_no">{{ st.reg_no }} — {{ st.name }} ({{ st.class_name }})</li>
                </ul>
              </div>
            </div>
          </div>
          <div class="flex items-center justify-end gap-2 border-t px-5 py-3">
            <button @click="showAdd = false" type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
            <button :disabled="submitting" @click="submitAdd" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ submitting ? 'Saving...' : 'Save Student' }}</button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

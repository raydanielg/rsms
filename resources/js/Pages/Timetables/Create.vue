<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, useForm } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'

const types = [
  { key: 'class', title: 'Class Timetable', desc: 'Lessons schedule per class', color: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
  { key: 'exam', title: 'Exams Timetable', desc: 'Examination schedule', color: 'bg-sky-100 text-sky-700 border-sky-200' },
  { key: 'supervision', title: 'Supervision Timetable', desc: 'Invigilation/Proctoring plan', color: 'bg-amber-100 text-amber-800 border-amber-200' },
  { key: 'teacher', title: 'Teacher Timetable', desc: 'Individual teacher schedule', color: 'bg-violet-100 text-violet-700 border-violet-200' },
]

const page = usePage()
const selected = ref(null)
const form = useForm({
  type: '',
  name: '',
  effective_date: '',
  scope_type: 'class',
  scope_name: '',
})
const canSubmit = computed(() => !!(selected.value && form.name && form.effective_date))

function pick(t) {
  selected.value = t
  // default scope based on type
  form.scope_type = t.key === 'teacher' ? 'teacher' : 'class'
  form.type = t.key
}

async function submit() {
  if (!canSubmit.value) return
  form.type = selected.value.key
  form.post(route('timetables.store'))
}

onMounted(() => {
  const q = page.props?.ziggy?.location || ''
  try {
    const url = new URL(q)
    const t = url.searchParams.get('type')
    const pre = types.find(x => x.key === t)
    if (pre) pick(pre)
  } catch {}
})
</script>

<template>
  <Head title="Create Timetable" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Timetable</h2>
    </template>

    <div class="space-y-6">
      <div>
        <div class="mb-2 text-sm font-medium text-slate-700">Choose Timetable Type</div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
          <button
            v-for="t in types"
            :key="t.key"
            type="button"
            @click="pick(t)"
            :class="['group rounded-xl border p-4 text-left shadow-sm hover:shadow transition', (selected && selected.key===t.key) ? 'ring-2 ring-green-500' : 'border-slate-200']"
          >
            <div :class="['inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold', t.color]">
              {{ t.title }}
            </div>
            <div class="mt-2 text-sm text-slate-600">{{ t.desc }}</div>
          </button>
        </div>
      </div>

      <div v-if="selected" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-3 text-sm font-medium text-slate-700">Timetable Details</div>
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700">Timetable Name</label>
            <input v-model.trim="form.name" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" placeholder="e.g., Form II A - Term 1" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Date</label>
            <input v-model="form.date" type="date" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Scope</label>
            <select v-model="form.scopeType" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
              <option value="class">Class</option>
              <option value="teacher">Teacher</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">{{ form.scopeType === 'class' ? 'Class Name' : 'Teacher Name' }}</label>
            <input v-model.trim="form.scopeValue" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" placeholder="e.g., Form I B or Mr. John" />
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
          <button @click="submit" :disabled="!canSubmit" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 disabled:opacity-60">Create</button>
          <button @click="selected=null" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
const props = defineProps({
  open: { type: Boolean, default: false },
});
const emit = defineEmits(['close']);

// Collapsible group state
const openGroups = ref({});
const isOpen = (label) => !!openGroups.value[label];
const toggleGroup = (label) => (openGroups.value[label] = !isOpen(label));

const menu = [
  { label: 'Dashboard', icon: 'home', route: 'dashboard' },
  { label: 'Student Management', icon: 'users', route: 'students.index' },
  {
    label: 'Classes Management', icon: 'folder', children: [
      { label: 'All Classes', route: 'classes.index' },
      // { label: 'Manage Classes' } // no route yet
    ]
  },
  {
    label: 'Subjects Management', icon: 'clipboard', children: [
      { label: 'All Subjects', route: 'subjects.index' },
      { label: 'Manage Subjects' },
    ]
  },
  {
    label: 'Information', icon: 'clipboard', children: [
      { label: 'Announcements', route: 'information.index', params: { tab: 'announcements' } },
      { label: 'Policies', route: 'information.index', params: { tab: 'policies' } },
      { label: 'Updates', route: 'information.index', params: { tab: 'updates' } },
    ]
  },
  { label: 'Teachers', icon: 'users', route: 'teachers.index' },
  {
    label: 'Results Management', icon: 'clipboard', children: [
      { label: 'All Results', route: 'results.index' },
      { label: 'Marking', route: 'marking.index' },
      // { label: 'Manage Results' } // no route yet
    ]
  },
  {
    label: 'Reports', icon: 'chart', children: [
      { label: 'Teachers Reports' },
      { label: 'Students Reports' },
      { label: 'School Report' },
    ]
  },
  {
    label: 'Timetables Management', icon: 'calendar', children: [
      { label: 'Class Timetables', route: 'timetables.class.index' },
      { label: 'All Timetables', route: 'timetables.index' },
      { label: 'Create Timetable', route: 'timetables.create' },
      { label: 'Sitting Plan', route: 'timetables.sitting' },
    ]
  },
];

function iconPath(name) {
  switch (name) {
    case 'home': return 'M3 9.75 12 3l9 6.75V21a.75.75 0 0 1-.75.75H14.5a.75.75 0 0 1-.75-.75v-4.5h-3.5V21a.75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 21V9.75z';
    case 'users': return 'M15 19.127a9.024 9.024 0 0 0 6 0V18a6 6 0 1 0-12 0v1.127a9.024 9.024 0 0 0 6 0zm-9-2.127v1.127a9.04 9.04 0 0 1-3-.527V15a4 4 0 1 1 8 0v2.5a9.04 9.04 0 0 1-3 .5 9.04 9.04 0 0 1-2-.5z';
    case 'folder': return 'M3 7.5A1.5 1.5 0 0 1 4.5 6h4.879a1.5 1.5 0 0 1 1.06.44l1.121 1.12H19.5A1.5 1.5 0 0 1 21 9v8.25A1.5 1.5 0 0 1 19.5 18.75H4.5A1.5 1.5 0 0 1 3 17.25V7.5z';
    case 'calendar': return 'M6.75 3v2.25m10.5-2.25V5.25M3 9.75h18M5.25 6.75h13.5A2.25 2.25 0 0 1 21 9v9.75A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75V9A2.25 2.25 0 0 1 5.25 6.75z';
    case 'clipboard': return 'M9 3.75h6A2.25 2.25 0 0 1 17.25 6v12A2.25 2.25 0 0 1 15 20.25H9A2.25 2.25 0 0 1 6.75 18V6A2.25 2.25 0 0 1 9 3.75zm0 0V6h6V3.75M9 9.75h6m-6 3h6m-6 3H12';
    case 'chart': return 'M3.75 19.5h16.5m-12-3.75V9.75m4.5 5.25V6.75m4.5 9V12';
    default: return '';
  }
}
</script>

<template>
  <!-- Mobile overlay -->
  <div v-if="open" class="fixed inset-0 z-40 bg-black/50 sm:hidden" @click="emit('close')"></div>

  <!-- Mobile off-canvas sidebar -->
  <div
    class="fixed left-0 top-16 z-50 h-[calc(100vh-4rem)] w-64 transform border-r border-gray-200 bg-white p-4 shadow-lg transition sm:hidden"
    :class="open ? 'translate-x-0' : '-translate-x-full'"
    role="dialog"
    aria-modal="true"
  >
    <div class="mb-6 flex items-center gap-2">
      <span class="rounded-md bg-green-600 px-2 py-1 text-xs font-bold tracking-wide text-white">MENU</span>
      <span class="text-xs uppercase tracking-wide text-gray-500">Education</span>
    </div>

    <nav class="space-y-1">
      <template v-for="item in menu" :key="item.label">
        <template v-if="item.children && item.children.length">
          <button type="button" @click="toggleGroup(item.label)" class="mt-2 flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700">
            <span class="inline-flex items-center gap-3">
              <svg class="h-5 w-5 text-gray-400 group-hover:text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath(item.icon)" />
              </svg>
              {{ item.label }}
            </span>
            <svg class="h-4 w-4 text-gray-400 transition-transform" :class="isOpen(item.label) ? 'rotate-90' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6.293 7.293a1 1 0 011.414 0L12 11.586l4.293-4.293a1 1 0 111.414 1.414l-5 5a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
          </button>
          <transition name="fade" appear>
            <div v-if="isOpen(item.label)" class="mt-1 space-y-1 ps-6">
              <template v-for="child in item.children" :key="child.label">
                <Link :href="child.route ? route(child.route, child.params || {}) : '#'" @click="emit('close')" class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700">
                  <span class="inline-flex items-center gap-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-gray-300 group-hover:bg-green-600"></span>
                    {{ child.label }}
                  </span>
                </Link>
              </template>
            </div>
          </transition>
        </template>
        <template v-else>
          <Link :href="item.route ? route(item.route, item.params || {}) : '#'" @click="emit('close')" class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700">
            <span class="inline-flex items-center gap-3">
              <svg class="h-5 w-5 text-gray-400 group-hover:text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath(item.icon)" />
              </svg>
              {{ item.label }}
            </span>
            <span v-if="item.badge" class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">{{ item.badge }}</span>
          </Link>
        </template>
      </template>
    </nav>

    <div class="mt-6 border-t border-gray-200 pt-4">
      <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Resources</div>
      <div class="space-y-1">
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">S</span>
          Syllabuses
        </Link>
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">P</span>
          Past Papers
        </Link>
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">N</span>
          Notes
        </Link>
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">L</span>
          Lesson Plan
        </Link>
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">M</span>
          More
        </Link>
      </div>
    </div>
  </div>

  <!-- Desktop sidebar -->
  <aside class="hidden fixed left-0 top-16 z-30 h-[calc(100vh-4rem)] w-64 border-r border-gray-200 bg-white/90 p-4 sm:block">
    <div class="mb-6 flex items-center gap-2">
      <span class="rounded-md bg-green-600 px-2 py-1 text-xs font-bold tracking-wide text-white">MENU</span>
      <span class="text-xs uppercase tracking-wide text-gray-500">Education</span>
    </div>

    <nav class="space-y-1">
      <template v-for="item in menu" :key="item.label">
        <template v-if="item.children && item.children.length">
          <button type="button" @click="toggleGroup(item.label)" class="mt-2 flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700">
            <span class="inline-flex items-center gap-3">
              <svg class="h-5 w-5 text-gray-400 group-hover:text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath(item.icon)" />
              </svg>
              {{ item.label }}
            </span>
            <svg class="h-4 w-4 text-gray-400 transition-transform" :class="isOpen(item.label) ? 'rotate-90' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6.293 7.293a1 1 0 011.414 0L12 11.586l4.293-4.293a1 1 0 111.414 1.414l-5 5a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
          </button>
          <transition name="fade" appear>
            <div v-if="isOpen(item.label)" class="mt-1 space-y-1 ps-6">
              <template v-for="child in item.children" :key="child.label">
                <Link :href="child.route ? route(child.route, child.params || {}) : '#'" class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700">
                  <span class="inline-flex items-center gap-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-gray-300 group-hover:bg-green-600"></span>
                    {{ child.label }}
                  </span>
                </Link>
              </template>
            </div>
          </transition>
        </template>
        <template v-else>
          <Link :href="item.route ? route(item.route, item.params || {}) : '#'" class="group flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700">
            <span class="inline-flex items-center gap-3">
              <svg class="h-5 w-5 text-gray-400 group-hover:text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath(item.icon)" />
              </svg>
              {{ item.label }}
            </span>
            <span v-if="item.badge" class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">{{ item.badge }}</span>
          </Link>
        </template>
      </template>
    </nav>

    <div class="mt-6 border-t border-gray-200 pt-4">
      <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Resources</div>
      <div class="space-y-1">
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">S</span>
          Syllabuses
        </Link>
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">P</span>
          Past Papers
        </Link>
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">N</span>
          Notes
        </Link>
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">L</span>
          Lesson Plan
        </Link>
        <Link href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-500">M</span>
          More
        </Link>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

const showingNavigationDropdown = ref(false);
const mobileSidebarOpen = ref(false);

// Global toast handling from Inertia flash props
const page = usePage();
const flash = computed(() => page.props.flash || {});
const toasts = ref([]);
let toastId = 0;
function pushToast(type, message) {
  if (!message) return;
  const id = ++toastId;
  toasts.value.push({ id, type, message });
  setTimeout(() => {
    toasts.value = toasts.value.filter(t => t.id !== id);
  }, 4000);
}
watch(flash, (val) => {
  if (!val) return;
  if (val.success) pushToast('success', val.success);
  if (val.error) pushToast('error', val.error);
  if (val.info) pushToast('info', val.info);
  if (val.warning) pushToast('warning', val.warning);
}, { immediate: true, deep: true });

// Allow pages to trigger toasts via window events
if (typeof window !== 'undefined') {
  window.addEventListener('toast', (e) => {
    const d = e.detail || {}; pushToast(d.type || 'info', d.message || '');
  });
  // helper
  window.__toast = (type, message) => pushToast(type, message);
}

const userName = computed(() => (page.props && page.props.auth && page.props.auth.user && page.props.auth.user.name) ? page.props.auth.user.name : 'User')
const userInitials = computed(() => userName.value.split(' ').map(w => (w && w[0]) || '').filter(Boolean).slice(0,2).join('').toUpperCase())
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-green-700 bg-green-600 text-white shadow-sm"
            >
                <!-- Primary Navigation Menu -->
                <div class="w-full px-3 sm:px-4">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Left: brand and mobile toggle -->
                        <div class="flex items-center gap-2">
                            <button class="inline-flex items-center justify-center rounded-md p-2 text-white hover:text-brand-yellow focus:outline-none sm:hidden" @click="mobileSidebarOpen = true" aria-label="Open menu">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')" class="inline-flex items-center gap-2">
                                    <span class="rounded-md border border-brand-blue bg-brand-yellow px-3 py-1 text-sm font-bold tracking-wide text-brand-blue shadow-sm">RSMS</span>
                                    <span class="hidden text-sm font-medium text-white sm:inline">Result management system</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Center: alerts area -->
                        <div class="flex flex-1 items-center justify-center px-2">
                            <div class="w-full max-w-xl">
                                <slot name="alerts"></slot>
                            </div>
                        </div>

                        <!-- Right: actions and user -->
                        <div class="hidden items-center gap-4 sm:flex">
                            <div class="hidden items-center gap-3 sm:flex">
                                <button class="rounded-full p-2 text-white hover:text-brand-yellow" aria-label="Calendar">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 10h18M5 6.5h14A2.5 2.5 0 0 1 21.5 9v9A2.5 2.5 0 0 1 19 20.5H5A2.5 2.5 0 0 1 2.5 18V9A2.5 2.5 0 0 1 5 6.5Z"/></svg>
                                </button>
                                <button class="rounded-full p-2 text-white hover:text-brand-yellow" aria-label="Notifications">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082A4.001 4.001 0 0 1 8 17m10-5v3l2 2H4l2-2v-3a6 6 0 1 1 12 0Z"/></svg>
                                </button>
                                <a href="https://wa.me/" target="_blank" rel="noopener" class="rounded-full p-2 text-white hover:text-brand-yellow" aria-label="WhatsApp">
                                    <svg class="h-5 w-5" viewBox="0 0 32 32" fill="currentColor"><path d="M19.11 17.47a3.44 3.44 0 0 1-1.18-.24 9.83 9.83 0 0 1-3.2-2.09 8.9 8.9 0 0 1-1.94-2.6 3 3 0 0 1-.27-1.35 1 1 0 0 1 .32-.74.78.78 0 0 1 .58-.26h.44a.68.68 0 0 1 .5.22l.35.41c.14.17.3.36.46.55s.33.39.5.57a.74.74 0 0 1 .21.54.86.86 0 0 1-.09.36 1.12.12 0 0 0-.1.39.63.63 0 0 0 .18.48 4.27 4.27 0 0 0 .73.56 5.29 5.29 0 0 0 1.06.57.64.64 0 0 0 .26.06.55.55 0 0 0 .36-.15c.08-.08.17-.16.26-.25a.81.81 0 0 1 .57-.22.71.71 0 0 1 .32.07c.22.11.43.23.64.36l.57.36a.73.73 0 0 1 .33.54.78.78 0 0 1-.12.46 3 3 0 0 1-.25.33 2.38 2.38 0 0 1-.3.29 1.52 1.52 0 0 1-1 .36 4 4 0 0 1-.66-.07Z"/></svg>
                                </a>
                                <button class="rounded-full p-2 text-white hover:text-brand-yellow" aria-label="Updates">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M4 10a8 8 0 0 1 13.66-5.66M20 14A8 8 0 0 1 6.34 19.66"/></svg>
                                </button>
                            </div>

                            <div class="hidden h-6 w-px bg-white/30 sm:block" aria-hidden="true"></div>
                            <div class="hidden sm:ms-2 sm:flex sm:items-center">
                                <div class="relative ms-3">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span class="inline-flex cursor-pointer select-none items-center text-sm font-medium text-white hover:text-brand-yellow">
                                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">{{ userInitials }}</span>
                                                <span class="ml-2 hidden lg:inline">{{ userName }}</span>
                                                <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </template>
                                        <template #content>
                                            <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                            <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile: actions + user avatar -->
                        <div class="flex items-center gap-2 sm:hidden">
                            <button class="rounded-full p-2 text-white hover:text-brand-yellow" aria-label="Calendar">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 10h18M5 6.5h14A2.5 2.5 0 0 1 21.5 9v9A2.5 2.5 0 0 1 19 20.5H5A2.5 2.5 0 0 1 2.5 18V9A2.5 2.5 0 0 1 5 6.5Z"/></svg>
                            </button>
                            <button class="rounded-full p-2 text-white hover:text-brand-yellow" aria-label="Notifications">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082A4.001 4.001 0 0 1 8 17m10-5v3l2 2H4l2-2v-3a6 6 0 1 1 12 0Z"/></svg>
                            </button>
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex h-9 w-9 cursor-pointer select-none items-center justify-center rounded-full bg-white/20 text-white">
                                        {{ userInitials }}
                                    </span>
                                </template>
                                <template #content>
                                    <div class="px-4 py-2 text-sm text-gray-700">{{ userName }}</div>
                                    <div class="border-t"></div>
                                    <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu removed -->
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content with Sidebar (edge-to-edge) -->
            <main class="py-4">
                <!-- Fixed sidebar on the far-left; mobile via off-canvas -->
                <Sidebar :open="mobileSidebarOpen" @close="mobileSidebarOpen = false" />
                <div class="min-h-[calc(100vh-4rem)] flex-1 sm:ml-64">
                    <div class="p-3 sm:p-4">
                        <div class="min-h-[70vh] rounded-xl bg-white/90 p-4 shadow-sm ring-1 ring-black/5">
                            <slot />
                        </div>
                    </div>
                </div>
            </main>
            <!-- Global Toasts -->
            <div class="pointer-events-none fixed right-4 top-4 z-50 space-y-2">
              <transition-group name="fade" tag="div">
                <div v-for="t in toasts" :key="t.id"
                     class="pointer-events-auto flex items-start gap-2 rounded-md border px-4 py-3 text-sm shadow">
                  <div v-if="t.type==='success'" class="rounded-full bg-emerald-100 p-1 text-emerald-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <div v-else-if="t.type==='error'" class="rounded-full bg-rose-100 p-1 text-rose-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                  </div>
                  <div v-else-if="t.type==='warning'" class="rounded-full bg-amber-100 p-1 text-amber-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                  </div>
                  <div v-else class="rounded-full bg-blue-100 p-1 text-blue-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 110-16 8 8 0 010 16z"/></svg>
                  </div>
                  <div :class="{
                    'text-emerald-800': t.type==='success',
                    'text-rose-800': t.type==='error',
                    'text-amber-800': t.type==='warning',
                    'text-blue-800': t.type==='info',
                  }">{{ t.message }}</div>
                </div>
              </transition-group>
            </div>
        </div>
    </div>
</template>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

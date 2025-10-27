<script setup>
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import { Link } from '@inertiajs/vue3';

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

const userName = computed(() => (page.props && page.props.auth && page.props.auth.user && page.props.auth.user.name) ? page.props.auth.user.name : 'Admin')
const userInitials = computed(() => userName.value.split(' ').map(w => (w && w[0]) || '').filter(Boolean).slice(0,2).join('').toUpperCase())
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-blue-800 bg-gradient-to-r from-blue-900 to-blue-700 text-white shadow-lg">
                <!-- Primary Navigation Menu -->
                <div class="w-full px-3 sm:px-4">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Left: brand and mobile toggle -->
                        <div class="flex items-center gap-2">
                            <button class="inline-flex items-center justify-center rounded-md p-2 text-white hover:text-yellow-300 focus:outline-none sm:hidden" @click="mobileSidebarOpen = true" aria-label="Open menu">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('admin.dashboard')" class="inline-flex items-center gap-2">
                                    <span class="rounded-md border-2 border-yellow-400 bg-yellow-400 px-3 py-1 text-sm font-bold tracking-wide text-blue-900 shadow-md">ADMIN</span>
                                    <span class="hidden text-sm font-medium text-white sm:inline">Administration Panel</span>
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
                                <button class="rounded-full p-2 text-white hover:text-yellow-300" aria-label="Notifications">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082A4.001 4.001 0 0 1 8 17m10-5v3l2 2H4l2-2v-3a6 6 0 1 1 12 0Z"/></svg>
                                </button>
                                <button class="rounded-full p-2 text-white hover:bg-blue-800 hover:text-yellow-400 transition-colors" aria-label="Settings">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                            </div>

                            <div class="hidden h-6 w-px bg-white/30 sm:block" aria-hidden="true"></div>
                            <div class="hidden sm:ms-2 sm:flex sm:items-center">
                                <div class="relative ms-3">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span class="inline-flex cursor-pointer select-none items-center text-sm font-medium text-white hover:text-yellow-300">
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
                            <button class="rounded-full p-2 text-white hover:text-yellow-300" aria-label="Notifications">
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
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content with Admin Sidebar -->
            <main>
                <!-- Mobile Overlay -->
                <div v-if="mobileSidebarOpen" @click="mobileSidebarOpen = false" class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm sm:hidden" style="top: 64px;"></div>
                
                <!-- Admin Sidebar -->
                <AdminSidebar :open="mobileSidebarOpen" @close="mobileSidebarOpen = false" />

                <!-- Main Content Area -->
                <div class="min-h-[calc(100vh-4rem)] flex-1 pt-4 sm:ml-64">
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
                     class="pointer-events-auto flex items-start gap-2 rounded-md border px-4 py-3 text-sm shadow-lg bg-white">
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

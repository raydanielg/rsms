<script setup>
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const mobileSidebarOpen = ref(false);

// Global toast handling
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

watch(flash, (newFlash) => {
  if (newFlash.success) pushToast('success', newFlash.success);
  if (newFlash.error) pushToast('error', newFlash.error);
  if (newFlash.warning) pushToast('warning', newFlash.warning);
  if (newFlash.info) pushToast('info', newFlash.info);
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-50">
            <!-- Navigation -->
            <nav class="bg-green-600 border-b border-green-700 shadow-lg">
                <div class="mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Left: brand and mobile toggle -->
                        <div class="flex items-center gap-3">
                            <button class="inline-flex items-center justify-center rounded-md p-2 text-white hover:bg-white/20 focus:outline-none sm:hidden" @click="mobileSidebarOpen = true" aria-label="Open menu">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="flex shrink-0 items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-400 border-2 border-yellow-300 shadow-md">
                                    <svg class="h-6 w-6 text-green-800" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" fill="currentColor"/>
                                    </svg>
                                </div>
                                <Link :href="route('emas.marker.dashboard')" class="flex flex-col">
                                    <span class="text-lg font-bold text-yellow-300 tracking-tight">EMAS Marker Panel</span>
                                    <span class="hidden text-xs text-yellow-100 sm:inline">Marks Entry System</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Right: actions and user -->
                        <div class="flex items-center gap-2">
                            <button class="rounded-lg p-2 text-white hover:bg-white/20 transition-colors" aria-label="Notifications">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082A4.001 4.001 0 0 1 8 17m10-5v3l2 2H4l2-2v-3a6 6 0 1 1 12 0Z"/>
                                </svg>
                            </button>
                            
                            <div class="h-8 w-px bg-white/30 mx-2" aria-hidden="true"></div>
                            
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-white/20 transition-colors">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-400 text-green-800 font-bold shadow-md border-2 border-yellow-300">
                                            M
                                        </div>
                                        <span class="hidden sm:inline">Marker</span>
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('emas.marker.dashboard')">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                            Dashboard
                                        </div>
                                    </DropdownLink>
                                    <DropdownLink :href="route('emas.logout')" method="post" as="button">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Log Out
                                        </div>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content with Sidebar -->
            <main>
                <!-- Mobile Overlay -->
                <div v-if="mobileSidebarOpen" @click="mobileSidebarOpen = false" class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm sm:hidden" style="top: 64px;"></div>
                
                <!-- Sidebar -->
                <div class="fixed left-0 z-40 w-64 transform bg-white shadow-lg transition-transform duration-300 ease-in-out" :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'" style="top: 64px; bottom: 0;">
                    <div class="flex h-full flex-col">
                        <!-- Close button for mobile -->
                        <div class="flex items-center justify-between border-b p-4 sm:hidden">
                            <span class="text-lg font-semibold text-gray-800">Marker Menu</span>
                            <button @click="mobileSidebarOpen = false" class="rounded-md p-2 text-gray-600 hover:bg-gray-100">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Navigation Links -->
                        <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                            <Link :href="route('emas.marker.dashboard')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors" :class="{ 'bg-gradient-to-r from-yellow-100 to-green-50 text-green-800 font-semibold border-l-4 border-green-600': $page.url === '/emas/marker/dashboard' }">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>

                            <!-- Marks Entry -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-green-700">Marks Entry</p>
                                <div class="mt-2 space-y-1">
                                    <Link :href="route('emas.marker.dashboard')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Assigned Exams
                                    </Link>
                                    <Link :href="route('emas.marker.history')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Marking History
                                    </Link>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-green-700">Quick Stats</p>
                                <div class="mt-2 space-y-2 px-4">
                                    <div class="rounded-lg bg-gradient-to-br from-yellow-50 to-amber-50 p-3 border border-yellow-200">
                                        <p class="text-xs text-gray-600 font-medium">Marks Entered Today</p>
                                        <p class="text-2xl font-bold text-green-700 mt-1">--</p>
                                    </div>
                                    <div class="rounded-lg bg-gradient-to-br from-green-50 to-emerald-50 p-3 border border-green-200">
                                        <p class="text-xs text-gray-600 font-medium">Total Marked</p>
                                        <p class="text-2xl font-bold text-green-700 mt-1">--</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Help & Support -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-green-700">Msaada</p>
                                <div class="mt-2 space-y-1">
                                    <Link :href="route('emas.marker.help')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors" :class="{ 'bg-gradient-to-r from-yellow-100 to-green-50 text-green-800 font-semibold border-l-4 border-green-600': $page.url.includes('/emas/marker/help') }">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Mwongozo
                                    </Link>
                                    <Link :href="route('emas.marker.support')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors" :class="{ 'bg-gradient-to-r from-yellow-100 to-green-50 text-green-800 font-semibold border-l-4 border-green-600': $page.url.includes('/emas/marker/support') }">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Wasiliana Na Support
                                    </Link>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>

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
                <div v-for="toast in toasts" :key="toast.id" class="pointer-events-auto transform transition-all duration-300 ease-out">
                    <div class="rounded-lg shadow-lg" :class="{
                        'bg-green-50 border border-green-200': toast.type === 'success',
                        'bg-red-50 border border-red-200': toast.type === 'error',
                        'bg-yellow-50 border border-yellow-200': toast.type === 'warning',
                        'bg-blue-50 border border-blue-200': toast.type === 'info'
                    }">
                        <div class="p-4">
                            <p class="text-sm font-medium" :class="{
                                'text-green-800': toast.type === 'success',
                                'text-red-800': toast.type === 'error',
                                'text-yellow-800': toast.type === 'warning',
                                'text-blue-800': toast.type === 'info'
                            }">{{ toast.message }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

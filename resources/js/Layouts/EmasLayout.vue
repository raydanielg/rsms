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
            <nav class="bg-emerald-600 border-b border-emerald-700 shadow-lg">
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
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm border border-white/30">
                                    <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                                    </svg>
                                </div>
                                <Link :href="route('emas.dashboard')" class="flex flex-col">
                                    <span class="text-lg font-bold text-white tracking-tight">EMAS</span>
                                    <span class="hidden text-xs text-white/80 sm:inline">Exam Management System</span>
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
                            
                            <button class="rounded-lg p-2 text-white hover:bg-white/20 transition-colors" aria-label="Settings">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                            
                            <div class="h-8 w-px bg-white/30 mx-2" aria-hidden="true"></div>
                            
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-white/20 transition-colors">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-emerald-600 font-bold shadow-md">
                                            E
                                        </div>
                                        <span class="hidden sm:inline">EMAS User</span>
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('emas.dashboard')">
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
                            <span class="text-lg font-semibold text-gray-800">EMAS Menu</span>
                            <button @click="mobileSidebarOpen = false" class="rounded-md p-2 text-gray-600 hover:bg-gray-100">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Navigation Links -->
                        <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                            <Link :href="route('emas.dashboard')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors" :class="{ 'bg-gradient-to-r from-emerald-100 to-emerald-50 text-emerald-800 font-semibold border-l-4 border-emerald-600': $page.url === '/emas/dashboard' }">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>

                            <!-- Exams Management -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-emerald-600">Exams Management</p>
                                <div class="mt-2 space-y-1">
                                    <Link :href="route('emas.exams.index')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        All Exams
                                    </Link>
                                    <Link :href="route('emas.exams.create')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Create Exam
                                    </Link>
                                    <Link :href="route('emas.exams.index')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Schedule
                                    </Link>
                                </div>
                            </div>

                            <!-- Centers & Venues -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-emerald-600">Centers & Venues</p>
                                <div class="mt-2 space-y-1">
                                    <Link :href="route('emas.centers.index')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Exam Centers
                                    </Link>
                                    <Link :href="route('emas.centers.index')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Locations
                                    </Link>
                                </div>
                            </div>

                            <!-- Candidates -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-emerald-600">Candidates</p>
                                <div class="mt-2 space-y-1">
                                    <Link :href="route('emas.candidates.index')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        All Candidates
                                    </Link>
                                    <Link :href="route('emas.candidates.create')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        Register Candidate
                                    </Link>
                                </div>
                            </div>

                            <!-- Results -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-emerald-600">Results</p>
                                <div class="mt-2 space-y-1">
                                    <Link :href="route('emas.results.index')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        View Results
                                    </Link>
                                    <Link :href="route('emas.results.upload')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        Upload Results
                                    </Link>
                                </div>
                            </div>

                            <!-- Reports -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-emerald-600">Reports</p>
                                <div class="mt-2 space-y-1">
                                    <Link :href="route('emas.reports.index')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Analytics
                                    </Link>
                                    <Link :href="route('emas.reports.index')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Generate Reports
                                    </Link>
                                </div>
                            </div>

                            <!-- Markers -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-emerald-600">Markers</p>
                                <div class="mt-2 space-y-1">
                                    <Link :href="route('emas.markers.index')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        All Markers
                                    </Link>
                                    <Link :href="route('emas.markers.create')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                        Add Marker
                                    </Link>
                                    <Link :href="route('emas.markers.assignments')" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        Assignments
                                    </Link>
                                </div>
                            </div>

                            <!-- System -->
                            <div class="pt-4">
                                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-emerald-600">System</p>
                                <div class="mt-2 space-y-1">
                                    <Link href="#" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Settings
                                    </Link>
                                    <Link href="#" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Activity Logs
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

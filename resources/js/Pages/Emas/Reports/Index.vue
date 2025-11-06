<script setup>
import { Head, Link } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref } from 'vue';

const selectedReportType = ref('');
const selectedExam = ref('');
const selectedPeriod = ref('monthly');
</script>

<template>
    <Head title="Reports & Analytics - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Reports & Analytics</h1>
                <p class="mt-2 text-base text-gray-600">Select a report type to view detailed examination analysis</p>
            </div>

            <!-- Report Types -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- General Reports -->
                <Link href="#" class="rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="rounded-full bg-white/20 px-3 py-1">
                            <span class="text-xs font-bold text-white">Overall</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">General Reports</h3>
                    <p class="text-sm text-emerald-50">Overall examination statistics and performance summary</p>
                </Link>

                <!-- Schools Reports -->
                <Link href="#" class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="rounded-full bg-white/20 px-3 py-1">
                            <span class="text-xs font-bold text-white">Schools</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Schools Reports</h3>
                    <p class="text-sm text-blue-50">Individual school performance and detailed analysis</p>
                </Link>

                <!-- Schools Ranks (Top 10) -->
                <Link :href="route('emas.reports.school-ranks')" class="rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 p-6 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div class="rounded-full bg-white/20 px-3 py-1">
                            <span class="text-xs font-bold text-white">Top 10</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Schools Rankings</h3>
                    <p class="text-sm text-amber-50">Top 10 best performing schools and rankings</p>
                </Link>

                <!-- Top Students -->
                <Link :href="route('emas.reports.top-students-filters')" class="rounded-xl bg-gradient-to-br from-red-500 to-pink-600 p-6 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div class="rounded-full bg-white/20 px-3 py-1">
                            <span class="text-xs font-bold text-white">Top 10</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Top Students</h3>
                    <p class="text-sm text-red-50">Best & weak students rankings with detailed analysis</p>
                </Link>

                <!-- Ward Ranks -->
                <Link :href="route('emas.reports.ward-filters')" class="rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 p-6 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <div class="rounded-full bg-white/20 px-3 py-1">
                            <span class="text-xs font-bold text-white">Location</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Ward Rankings</h3>
                    <p class="text-sm text-purple-50">Performance comparison across different wards</p>
                </Link>

                <!-- Subject Statistics -->
                <Link :href="route('emas.reports.subject-statistics')" class="rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 p-6 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="rounded-full bg-white/20 px-3 py-1">
                            <span class="text-xs font-bold text-white">Subjects</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Subject Statistics</h3>
                    <p class="text-sm text-teal-50">Subject-wise performance analysis and trends</p>
                </Link>
            </div>

        </div>
    </EmasLayout>
</template>

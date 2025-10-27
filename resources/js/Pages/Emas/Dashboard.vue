<script setup>
import { Head } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

defineProps({
    stats: {
        type: Object,
        default: () => ({})
    }
});
</script>

<template>
    <Head title="EMAS Dashboard" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Welcome Section -->
            <div class="rounded-lg bg-gradient-to-r from-emerald-600 to-green-600 p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">Welcome to EMAS Dashboard</h2>
                        <p class="mt-1 text-emerald-100">Exam Management & Administration System</p>
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-right">
                            <p class="text-sm text-emerald-100">Today</p>
                            <p class="text-lg font-semibold">{{ new Date().toLocaleDateString() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-6 shadow-md ring-1 ring-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Exams</p>
                            <p class="mt-2 text-3xl font-bold text-green-700">{{ stats.exams }}</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="h-8 w-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-md ring-1 ring-emerald-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Exam Centers</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ stats.centers }}</p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-3">
                            <svg class="h-8 w-8 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-md ring-1 ring-teal-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Candidates</p>
                            <p class="mt-2 text-3xl font-bold text-teal-700">{{ stats.candidates }}</p>
                        </div>
                        <div class="rounded-full bg-teal-100 p-3">
                            <svg class="h-8 w-8 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-md ring-1 ring-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Exam Papers</p>
                            <p class="mt-2 text-3xl font-bold text-green-700">{{ stats.papers }}</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="h-8 w-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts & Activity Section -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Exam Status Chart -->
                <div class="rounded-lg bg-white p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Status Overview</h3>
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <div class="relative inline-flex items-center justify-center">
                                <!-- Simple Pie Chart Representation -->
                                <svg class="w-48 h-48" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#e5e7eb" stroke-width="20"/>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="20" 
                                            stroke-dasharray="75 251" stroke-dashoffset="0" transform="rotate(-90 50 50)"/>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="20" 
                                            stroke-dasharray="50 251" stroke-dashoffset="-75" transform="rotate(-90 50 50)"/>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="20" 
                                            stroke-dasharray="25 251" stroke-dashoffset="-125" transform="rotate(-90 50 50)"/>
                                </svg>
                            </div>
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                    <span class="text-sm text-gray-600">Completed (60%)</span>
                                </div>
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                    <span class="text-sm text-gray-600">In Progress (30%)</span>
                                </div>
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                    <span class="text-sm text-gray-600">Pending (10%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="rounded-lg bg-white p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3 pb-4 border-b border-gray-100">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100">
                                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Exam Created</p>
                                <p class="text-xs text-gray-500">Mathematics Final Exam 2025</p>
                                <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 pb-4 border-b border-gray-100">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                                <svg class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">New Candidate Registered</p>
                                <p class="text-xs text-gray-500">John Doe - Center EC001</p>
                                <p class="text-xs text-gray-400 mt-1">5 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 pb-4 border-b border-gray-100">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100">
                                <svg class="h-4 w-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Results Uploaded</p>
                                <p class="text-xs text-gray-500">Physics Exam - 45 candidates</p>
                                <p class="text-xs text-gray-400 mt-1">1 day ago</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100">
                                <svg class="h-4 w-4 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Report Generated</p>
                                <p class="text-xs text-gray-500">Monthly Performance Report</p>
                                <p class="text-xs text-gray-400 mt-1">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <button class="flex flex-col items-center gap-3 rounded-lg border-2 border-emerald-200 p-4 hover:border-emerald-400 hover:bg-emerald-50 transition-all">
                        <div class="rounded-full bg-emerald-100 p-3">
                            <svg class="h-6 w-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Create Exam</span>
                    </button>
                    <button class="flex flex-col items-center gap-3 rounded-lg border-2 border-blue-200 p-4 hover:border-blue-400 hover:bg-blue-50 transition-all">
                        <div class="rounded-full bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Add Candidate</span>
                    </button>
                    <button class="flex flex-col items-center gap-3 rounded-lg border-2 border-amber-200 p-4 hover:border-amber-400 hover:bg-amber-50 transition-all">
                        <div class="rounded-full bg-amber-100 p-3">
                            <svg class="h-6 w-6 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Upload Results</span>
                    </button>
                    <button class="flex flex-col items-center gap-3 rounded-lg border-2 border-purple-200 p-4 hover:border-purple-400 hover:bg-purple-50 transition-all">
                        <div class="rounded-full bg-purple-100 p-3">
                            <svg class="h-6 w-6 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">View Reports</span>
                    </button>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

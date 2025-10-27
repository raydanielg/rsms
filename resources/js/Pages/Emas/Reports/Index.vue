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
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
                    <p class="mt-1 text-sm text-gray-600">Generate and view examination reports</p>
                </div>
                <button class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Report
                </button>
            </div>

            <!-- Report Types -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-6 shadow-sm hover:shadow-md transition-shadow cursor-pointer border-2 border-transparent hover:border-emerald-500">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100">
                            <svg class="h-6 w-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-semibold text-gray-900">Performance Report</h3>
                    <p class="mt-1 text-sm text-gray-500">Overall exam performance analysis</p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm hover:shadow-md transition-shadow cursor-pointer border-2 border-transparent hover:border-blue-500">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-semibold text-gray-900">Candidate Report</h3>
                    <p class="mt-1 text-sm text-gray-500">Individual candidate statistics</p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm hover:shadow-md transition-shadow cursor-pointer border-2 border-transparent hover:border-purple-500">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                            <svg class="h-6 w-6 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-semibold text-gray-900">Center Report</h3>
                    <p class="mt-1 text-sm text-gray-500">Center-wise performance data</p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm hover:shadow-md transition-shadow cursor-pointer border-2 border-transparent hover:border-amber-500">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100">
                            <svg class="h-6 w-6 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-semibold text-gray-900">Subject Analysis</h3>
                    <p class="mt-1 text-sm text-gray-500">Subject-wise performance trends</p>
                </div>
            </div>

            <!-- Report Generator -->
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Generate Custom Report</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                        <select v-model="selectedReportType" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="">Select Type</option>
                            <option value="performance">Performance Report</option>
                            <option value="candidate">Candidate Report</option>
                            <option value="center">Center Report</option>
                            <option value="subject">Subject Analysis</option>
                            <option value="comparative">Comparative Analysis</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
                        <select v-model="selectedExam" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="">All Exams</option>
                            <option value="1">Mathematics Final</option>
                            <option value="2">English Language</option>
                            <option value="3">Physics Practical</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                        <select v-model="selectedPeriod" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="weekly">This Week</option>
                            <option value="monthly">This Month</option>
                            <option value="quarterly">This Quarter</option>
                            <option value="yearly">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Overview -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Pass Rate Trend -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pass Rate Trend</h3>
                    <div class="h-64 flex items-end justify-between gap-2">
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-emerald-500 rounded-t" style="height: 75%"></div>
                            <span class="mt-2 text-xs text-gray-600">Jan</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-emerald-500 rounded-t" style="height: 82%"></div>
                            <span class="mt-2 text-xs text-gray-600">Feb</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-emerald-500 rounded-t" style="height: 68%"></div>
                            <span class="mt-2 text-xs text-gray-600">Mar</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-emerald-500 rounded-t" style="height: 78%"></div>
                            <span class="mt-2 text-xs text-gray-600">Apr</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-emerald-500 rounded-t" style="height: 85%"></div>
                            <span class="mt-2 text-xs text-gray-600">May</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-emerald-500 rounded-t" style="height: 79%"></div>
                            <span class="mt-2 text-xs text-gray-600">Jun</span>
                        </div>
                    </div>
                </div>

                <!-- Grade Distribution -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade Distribution</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">Grade A</span>
                                <span class="text-sm font-semibold text-emerald-600">25%</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-gray-200">
                                <div class="h-3 rounded-full bg-emerald-500" style="width: 25%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">Grade B</span>
                                <span class="text-sm font-semibold text-blue-600">35%</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-gray-200">
                                <div class="h-3 rounded-full bg-blue-500" style="width: 35%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">Grade C</span>
                                <span class="text-sm font-semibold text-amber-600">20%</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-gray-200">
                                <div class="h-3 rounded-full bg-amber-500" style="width: 20%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">Grade D</span>
                                <span class="text-sm font-semibold text-orange-600">12%</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-gray-200">
                                <div class="h-3 rounded-full bg-orange-500" style="width: 12%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">Grade F</span>
                                <span class="text-sm font-semibold text-red-600">8%</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-gray-200">
                                <div class="h-3 rounded-full bg-red-500" style="width: 8%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="rounded-lg bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Reports</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Report Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Generated</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100">
                                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Monthly Performance Report</p>
                                            <p class="text-sm text-gray-500">All exams included</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Performance
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">January 2025</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">2 hours ago</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 transition-colors" title="View">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button class="rounded-lg p-2 text-emerald-600 hover:bg-emerald-50 transition-colors" title="Download">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                            <svg class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Candidate Statistics Report</p>
                                            <p class="text-sm text-gray-500">Form 4 candidates</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                                        Candidate
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">Q4 2024</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">1 day ago</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 transition-colors">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button class="rounded-lg p-2 text-emerald-600 hover:bg-emerald-50 transition-colors">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

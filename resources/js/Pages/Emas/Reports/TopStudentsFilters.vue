<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const form = useForm({
    region: '',
    district: '',
    report_type: 'best', // 'best' or 'worst'
    school_type: 'all', // 'all' or 'government'
    gender: 'all', // 'all', 'male', 'female'
    limit: 10,
    year: new Date().getFullYear(),
    exam_type: 'pre-national',
});

const generateReport = () => {
    form.get(route('emas.reports.top-students'), {
        preserveState: false,
        preserveScroll: false,
    });
};
</script>

<template>
    <Head title="Top Students Report - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Top Students Performance Report</h1>
                <p class="mt-2 text-base text-gray-600">Select filters to view best performing or weak students</p>
            </div>

            <!-- Filters Card -->
            <div class="rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 p-8 shadow-xl border-2 border-amber-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-600">
                        <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900">Report Configuration</h2>
                        <p class="text-sm text-gray-600 mt-1">Configure parameters to view top performers or students needing support</p>
                    </div>
                </div>
                
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                Report Type <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.report_type" class="w-full rounded-lg border-2 border-amber-300 px-4 py-3 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 bg-white font-medium">
                            <option value="best">Top 10 Best Students</option>
                            <option value="worst">Top 10 Weak Students</option>
                        </select>
                    </div>

                    <!-- Region -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                Region <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.region" class="w-full rounded-lg border-2 border-amber-300 px-4 py-3 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 bg-white font-medium">
                            <option value="">Select Region</option>
                            <option value="mwanza">Mwanza</option>
                            <option value="dar">Dar es Salaam</option>
                            <option value="arusha">Arusha</option>
                        </select>
                    </div>

                    <!-- District -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                District <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.district" :disabled="!form.region" class="w-full rounded-lg border-2 border-amber-300 px-4 py-3 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 bg-white font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Select District</option>
                            <option value="ilemela">Ilemela MC</option>
                            <option value="nyamagana">Nyamagana MC</option>
                        </select>
                    </div>

                    <!-- School Type -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                School Type <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.school_type" class="w-full rounded-lg border-2 border-amber-300 px-4 py-3 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 bg-white font-medium">
                            <option value="all">All Schools</option>
                            <option value="government">Government Schools Only</option>
                            <option value="private">Private Schools Only</option>
                        </select>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                Gender <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.gender" class="w-full rounded-lg border-2 border-amber-300 px-4 py-3 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 bg-white font-medium">
                            <option value="all">All Students</option>
                            <option value="male">Male Students Only</option>
                            <option value="female">Female Students Only</option>
                        </select>
                    </div>

                    <!-- Year -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                Year <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.year" class="w-full rounded-lg border-2 border-amber-300 px-4 py-3 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 bg-white font-medium">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                </div>

                <!-- Info Box -->
                <div v-if="form.report_type === 'best'" class="mt-6 p-4 bg-emerald-100 border-l-4 border-emerald-600 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-emerald-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-emerald-900">Best Students Report</p>
                            <p class="text-xs text-emerald-700 mt-1">This will display top 10 students with highest average scores and best performance</p>
                        </div>
                    </div>
                </div>

                <div v-if="form.report_type === 'worst'" class="mt-6 p-4 bg-red-100 border-l-4 border-red-600 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-red-900">Weak Students Report</p>
                            <p class="text-xs text-red-700 mt-1">This will display 10 students with lowest performance who need academic support and intervention</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex items-center justify-between gap-4 pt-6 border-t-2 border-amber-200">
                    <div class="text-sm text-gray-600">
                        <span class="text-red-500 font-bold">*</span> Required fields
                    </div>
                    <div class="flex gap-4">
                        <button 
                            type="button"
                            @click="form.reset()"
                            class="px-6 py-3 rounded-lg border-2 border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition-all hover:scale-105"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </span>
                        </button>
                        <button 
                            @click="generateReport"
                            :disabled="!form.region || !form.district"
                            :class="[
                                'px-8 py-3 rounded-lg font-black transition-all shadow-lg',
                                !form.region || !form.district
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                    : 'bg-gradient-to-r from-amber-600 to-orange-600 text-white hover:from-amber-700 hover:to-orange-700 hover:scale-105 hover:shadow-xl'
                            ]"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Generate Report
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow-md border-l-4 border-emerald-500">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100">
                            <svg class="h-6 w-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Top Performers</p>
                            <p class="text-2xl font-black text-gray-900">10</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-md border-l-4 border-amber-500">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100">
                            <svg class="h-6 w-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Students</p>
                            <p class="text-2xl font-black text-gray-900">0</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-md border-l-4 border-red-500">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Need Support</p>
                            <p class="text-2xl font-black text-gray-900">10</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

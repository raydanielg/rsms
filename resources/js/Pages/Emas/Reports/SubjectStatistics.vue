<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

defineProps({
    regions: Array,
    districts: Array,
    wards: Array,
    subjects: Array,
});

const form = useForm({
    region: '',
    district: '',
    ward: '',
    subject: '',
    year: new Date().getFullYear(),
    exam_type: 'pre-national',
});

const generateReport = () => {
    // Navigate to school ranks with filters as query parameters
    form.get(route('emas.reports.school-ranks'), {
        preserveState: false,
        preserveScroll: false,
    });
};
</script>

<template>
    <Head title="Subject Statistics - EMAS Reports" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Subject Statistics & Reports</h1>
                <p class="mt-2 text-base text-gray-600">Select filters to generate subject-wise performance reports and school rankings</p>
            </div>

            <!-- Filters Card -->
            <div class="rounded-xl bg-gradient-to-br from-teal-50 to-cyan-50 p-8 shadow-xl border-2 border-teal-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-600">
                        <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900">Report Filters</h2>
                        <p class="text-sm text-gray-600 mt-1">Configure parameters to generate detailed subject analysis</p>
                    </div>
                </div>
                
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Region -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                Region <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.region" class="w-full rounded-lg border-2 border-teal-300 px-4 py-3 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 bg-white font-medium">
                            <option value="">Select Region</option>
                            <option value="mwanza">Mwanza</option>
                            <option value="dar">Dar es Salaam</option>
                            <option value="arusha">Arusha</option>
                            <option value="kilimanjaro">Kilimanjaro</option>
                            <option value="dodoma">Dodoma</option>
                        </select>
                        <p v-if="form.errors.region" class="mt-1 text-xs text-red-600">{{ form.errors.region }}</p>
                    </div>

                    <!-- District -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                District
                            </span>
                        </label>
                        <select v-model="form.district" :disabled="!form.region" class="w-full rounded-lg border-2 border-teal-300 px-4 py-3 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 bg-white font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">All Districts</option>
                            <option value="ilemela">Ilemela MC</option>
                            <option value="nyamagana">Nyamagana MC</option>
                            <option value="magu">Magu DC</option>
                        </select>
                    </div>

                    <!-- Ward -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                </svg>
                                Ward
                            </span>
                        </label>
                        <select v-model="form.ward" :disabled="!form.district" class="w-full rounded-lg border-2 border-teal-300 px-4 py-3 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 bg-white font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">All Wards</option>
                            <option value="ward1">Buhongwa</option>
                            <option value="ward2">Nyakato</option>
                            <option value="ward3">Igoma</option>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                </svg>
                                Subject <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.subject" class="w-full rounded-lg border-2 border-teal-300 px-4 py-3 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 bg-white font-medium">
                            <option value="">Select Subject</option>
                            <option value="civics">Civics</option>
                            <option value="mathematics">Mathematics</option>
                            <option value="english">English Language</option>
                            <option value="kiswahili">Kiswahili</option>
                            <option value="biology">Biology</option>
                            <option value="chemistry">Chemistry</option>
                            <option value="physics">Physics</option>
                            <option value="geography">Geography</option>
                            <option value="history">History</option>
                        </select>
                        <p v-if="form.errors.subject" class="mt-1 text-xs text-red-600">{{ form.errors.subject }}</p>
                    </div>

                    <!-- Year -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                Year <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.year" class="w-full rounded-lg border-2 border-teal-300 px-4 py-3 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 bg-white font-medium">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                            <option value="2022">2022</option>
                        </select>
                    </div>

                    <!-- Exam Type -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                Exam Type <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.exam_type" class="w-full rounded-lg border-2 border-teal-300 px-4 py-3 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 bg-white font-medium">
                            <option value="pre-national">Pre-National Examination</option>
                            <option value="mock">Mock Examination</option>
                            <option value="monthly">Monthly Test</option>
                            <option value="midterm">Mid-Term Examination</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex items-center justify-between gap-4 pt-6 border-t-2 border-teal-200">
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
                            :disabled="!form.region || !form.subject"
                            :class="[
                                'px-8 py-3 rounded-lg font-black transition-all shadow-lg',
                                !form.region || !form.subject
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                    : 'bg-gradient-to-r from-teal-600 to-cyan-600 text-white hover:from-teal-700 hover:to-cyan-700 hover:scale-105 hover:shadow-xl'
                            ]"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Generate Report
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow-md border-l-4 border-teal-500">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-100">
                            <svg class="h-6 w-6 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Subjects</p>
                            <p class="text-2xl font-black text-gray-900">24</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-md border-l-4 border-cyan-500">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-100">
                            <svg class="h-6 w-6 text-cyan-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Schools</p>
                            <p class="text-2xl font-black text-gray-900">0</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-md border-l-4 border-emerald-500">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100">
                            <svg class="h-6 w-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Students</p>
                            <p class="text-2xl font-black text-gray-900">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

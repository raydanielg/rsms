<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

defineProps({
    regions: Array,
    districts: Array,
    wards: Array,
});

const form = useForm({
    region: '',
    district: '',
    ward: '',
    year: new Date().getFullYear(),
    exam_type: 'pre-national',
    view_type: 'all', // 'all' or 'single'
});

const generateReport = () => {
    form.get(route('emas.reports.ward-ranks'), {
        preserveState: false,
        preserveScroll: false,
    });
};
</script>

<template>
    <Head title="Ward Performance Filters - EMAS Reports" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">Ward Performance Report</h1>
                <p class="mt-2 text-base text-gray-600">Select filters to generate ward performance rankings and statistics</p>
            </div>

            <!-- Filters Card -->
            <div class="rounded-xl bg-gradient-to-br from-purple-50 to-indigo-50 p-8 shadow-xl border-2 border-purple-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-600">
                        <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900">Report Filters</h2>
                        <p class="text-sm text-gray-600 mt-1">Configure parameters to generate detailed ward analysis</p>
                    </div>
                </div>
                
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Region -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                Region <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.region" class="w-full rounded-lg border-2 border-purple-300 px-4 py-3 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200 bg-white font-medium">
                            <option value="">Select Region</option>
                            <option value="mwanza">Mwanza</option>
                            <option value="dar">Dar es Salaam</option>
                            <option value="arusha">Arusha</option>
                            <option value="kilimanjaro">Kilimanjaro</option>
                            <option value="dodoma">Dodoma</option>
                        </select>
                    </div>

                    <!-- District -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                District <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.district" :disabled="!form.region" class="w-full rounded-lg border-2 border-purple-300 px-4 py-3 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200 bg-white font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Select District</option>
                            <option value="ilemela">Ilemela MC</option>
                            <option value="nyamagana">Nyamagana MC</option>
                            <option value="magu">Magu DC</option>
                        </select>
                    </div>

                    <!-- View Type -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                View Type <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.view_type" class="w-full rounded-lg border-2 border-purple-300 px-4 py-3 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200 bg-white font-medium">
                            <option value="all">All Wards (Summary)</option>
                            <option value="single">Specific Ward</option>
                        </select>
                    </div>

                    <!-- Ward (Only if single view) -->
                    <div v-if="form.view_type === 'single'">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                </svg>
                                Ward <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.ward" :disabled="!form.district" class="w-full rounded-lg border-2 border-purple-300 px-4 py-3 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200 bg-white font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Select Ward</option>
                            <option value="kawekamo">Kawekamo</option>
                            <option value="nyakato">Nyakato</option>
                            <option value="kiseke">Kiseke</option>
                            <option value="nyasaka">Nyasaka</option>
                            <option value="igoma">Igoma</option>
                        </select>
                    </div>

                    <!-- Year -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                Year <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.year" class="w-full rounded-lg border-2 border-purple-300 px-4 py-3 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200 bg-white font-medium">
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
                                <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                Exam Type <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select v-model="form.exam_type" class="w-full rounded-lg border-2 border-purple-300 px-4 py-3 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-200 bg-white font-medium">
                            <option value="pre-national">Pre-National Examination</option>
                            <option value="mock">Mock Examination</option>
                            <option value="monthly">Monthly Test</option>
                            <option value="midterm">Mid-Term Examination</option>
                        </select>
                    </div>
                </div>

                <!-- Info Box -->
                <div v-if="form.view_type === 'all'" class="mt-6 p-4 bg-purple-100 border-l-4 border-purple-600 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-purple-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-purple-900">All Wards View</p>
                            <p class="text-xs text-purple-700 mt-1">This will show performance summary for all wards in the selected district with rankings</p>
                        </div>
                    </div>
                </div>

                <div v-if="form.view_type === 'single'" class="mt-6 p-4 bg-indigo-100 border-l-4 border-indigo-600 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-indigo-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-indigo-900">Specific Ward View</p>
                            <p class="text-xs text-indigo-700 mt-1">This will show detailed performance analysis for the selected ward only</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex items-center justify-between gap-4 pt-6 border-t-2 border-purple-200">
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
                            :disabled="!form.region || !form.district || (form.view_type === 'single' && !form.ward)"
                            :class="[
                                'px-8 py-3 rounded-lg font-black transition-all shadow-lg',
                                !form.region || !form.district || (form.view_type === 'single' && !form.ward)
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                    : 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white hover:from-purple-700 hover:to-indigo-700 hover:scale-105 hover:shadow-xl'
                            ]"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Generate Ward Report
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow-md border-l-4 border-purple-500">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                            <svg class="h-6 w-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Wards</p>
                            <p class="text-2xl font-black text-gray-900">0</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-md border-l-4 border-indigo-500">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100">
                            <svg class="h-6 w-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Schools per Ward</p>
                            <p class="text-2xl font-black text-gray-900">~3</p>
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
                            <p class="text-sm font-medium text-gray-600">Students per Ward</p>
                            <p class="text-2xl font-black text-gray-900">~400</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

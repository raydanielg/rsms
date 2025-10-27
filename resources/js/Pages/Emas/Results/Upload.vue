<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref } from 'vue';

const form = useForm({
    exam_id: '',
    upload_method: 'file',
    file: null,
    results: []
});

const fileInput = ref(null);
const fileName = ref('');

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.file = file;
        fileName.value = file.name;
    }
};

const submit = () => {
    form.post(route('emas.results.store'));
};
</script>

<template>
    <Head title="Upload Results - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Upload Exam Results</h1>
                    <p class="mt-1 text-sm text-gray-600">Upload results via file or enter manually</p>
                </div>
                <Link :href="route('emas.results.index')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Results
                </Link>
            </div>

            <!-- Upload Method Selection -->
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Upload Method</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="relative flex cursor-pointer rounded-lg border-2 p-4 transition-all" :class="form.upload_method === 'file' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" v-model="form.upload_method" value="file" class="sr-only" />
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="form.upload_method === 'file' ? 'bg-emerald-100' : 'bg-gray-100'">
                                <svg class="h-6 w-6" :class="form.upload_method === 'file' ? 'text-emerald-600' : 'text-gray-600'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">Upload File</p>
                                <p class="mt-1 text-sm text-gray-500">Upload Excel or CSV file with results</p>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border-2 p-4 transition-all" :class="form.upload_method === 'manual' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" v-model="form.upload_method" value="manual" class="sr-only" />
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="form.upload_method === 'manual' ? 'bg-emerald-100' : 'bg-gray-100'">
                                <svg class="h-6 w-6" :class="form.upload_method === 'manual' ? 'text-emerald-600' : 'text-gray-600'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">Manual Entry</p>
                                <p class="mt-1 text-sm text-gray-500">Enter results manually one by one</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Exam Selection -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Exam</h2>
                    <div class="max-w-md">
                        <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-1">Examination *</label>
                        <select
                            v-model="form.exam_id"
                            id="exam_id"
                            required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        >
                            <option value="">Select Exam</option>
                            <option value="1">Mathematics Final Exam - MATH-2025-001</option>
                            <option value="2">English Language Exam - ENG-2025-002</option>
                            <option value="3">Physics Practical Exam - PHY-2025-003</option>
                            <option value="4">Chemistry Theory - CHEM-2025-004</option>
                            <option value="5">Biology Practical - BIO-2025-005</option>
                        </select>
                        <p v-if="form.errors.exam_id" class="mt-1 text-sm text-red-600">{{ form.errors.exam_id }}</p>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div v-if="form.upload_method === 'file'" class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload Results File</h2>
                    
                    <!-- File Upload Area -->
                    <div class="mb-6">
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-12 h-12 mb-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500">Excel (.xlsx, .xls) or CSV files (MAX. 10MB)</p>
                                    <p v-if="fileName" class="mt-3 text-sm font-medium text-emerald-600">Selected: {{ fileName }}</p>
                                </div>
                                <input ref="fileInput" type="file" class="hidden" accept=".xlsx,.xls,.csv" @change="handleFileChange" />
                            </label>
                        </div>
                        <p v-if="form.errors.file" class="mt-2 text-sm text-red-600">{{ form.errors.file }}</p>
                    </div>

                    <!-- Download Template -->
                    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-blue-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-900">Need a template?</p>
                                <p class="mt-1 text-sm text-blue-700">Download our Excel template to ensure your file is formatted correctly.</p>
                                <button type="button" class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download Template
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manual Entry Section -->
                <div v-if="form.upload_method === 'manual'" class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Enter Results Manually</h2>
                    
                    <div class="space-y-4">
                        <!-- Sample Manual Entry Form -->
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                    placeholder="CND-2025-001234"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Score (out of 100)</label>
                                <input
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                    placeholder="85"
                                />
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                                    Add Result
                                </button>
                            </div>
                        </div>

                        <!-- Added Results Preview -->
                        <div class="mt-6 rounded-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-700">Added Results (0)</p>
                            </div>
                            <div class="p-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm">No results added yet</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('emas.results.index')" class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing || !form.exam_id || (form.upload_method === 'file' && !form.file)"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors disabled:opacity-50"
                    >
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        {{ form.processing ? 'Uploading...' : 'Upload Results' }}
                    </button>
                </div>
            </form>
        </div>
    </EmasLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const form = useForm({
    exam_name: '',
    exam_code: '',
    subject: '',
    level: '',
    class_form: '',
    exam_date: '',
    start_time: '',
    end_time: '',
    duration: '',
    total_marks: '',
    pass_marks: '',
    description: '',
    instructions: '',
    exam_type: 'written',
    status: 'scheduled'
});

const submit = () => {
    form.post(route('emas.exams.store'), {
        onSuccess: () => {
            // Redirect handled by controller
        }
    });
};
</script>

<template>
    <Head title="Create Exam - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Exam</h1>
                    <p class="mt-1 text-sm text-gray-600">Set up a new examination session</p>
                </div>
                <Link :href="route('emas.exams.index')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Exams
                </Link>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="exam_name" class="block text-sm font-medium text-gray-700 mb-1">Exam Name *</label>
                            <input
                                v-model="form.exam_name"
                                type="text"
                                id="exam_name"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="e.g., Mathematics Final Exam"
                            />
                            <p v-if="form.errors.exam_name" class="mt-1 text-sm text-red-600">{{ form.errors.exam_name }}</p>
                        </div>

                        <div>
                            <label for="exam_code" class="block text-sm font-medium text-gray-700 mb-1">Exam Code *</label>
                            <input
                                v-model="form.exam_code"
                                type="text"
                                id="exam_code"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="e.g., MATH-2025-001"
                            />
                            <p v-if="form.errors.exam_code" class="mt-1 text-sm text-red-600">{{ form.errors.exam_code }}</p>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                            <select
                                v-model="form.subject"
                                id="subject"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            >
                                <option value="">Select Subject</option>
                                <option value="mathematics">Mathematics</option>
                                <option value="english">English Language</option>
                                <option value="kiswahili">Kiswahili</option>
                                <option value="physics">Physics</option>
                                <option value="chemistry">Chemistry</option>
                                <option value="biology">Biology</option>
                                <option value="geography">Geography</option>
                                <option value="history">History</option>
                                <option value="civics">Civics</option>
                                <option value="commerce">Commerce</option>
                            </select>
                            <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</p>
                        </div>

                        <div>
                            <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-1">Exam Type *</label>
                            <select
                                v-model="form.exam_type"
                                id="exam_type"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            >
                                <option value="written">Written Exam</option>
                                <option value="practical">Practical Exam</option>
                                <option value="oral">Oral Exam</option>
                                <option value="project">Project Assessment</option>
                            </select>
                            <p v-if="form.errors.exam_type" class="mt-1 text-sm text-red-600">{{ form.errors.exam_type }}</p>
                        </div>

                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Education Level *</label>
                            <select
                                v-model="form.level"
                                id="level"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            >
                                <option value="">Select Level</option>
                                <option value="primary">Primary Education</option>
                                <option value="secondary">Secondary Education (O-Level)</option>
                                <option value="advanced">Advanced Level (A-Level)</option>
                            </select>
                            <p v-if="form.errors.level" class="mt-1 text-sm text-red-600">{{ form.errors.level }}</p>
                        </div>

                        <div>
                            <label for="class_form" class="block text-sm font-medium text-gray-700 mb-1">Class/Form *</label>
                            <select
                                v-model="form.class_form"
                                id="class_form"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            >
                                <option value="">Select Class/Form</option>
                                <optgroup label="Primary">
                                    <option value="std1">Standard 1</option>
                                    <option value="std2">Standard 2</option>
                                    <option value="std3">Standard 3</option>
                                    <option value="std4">Standard 4</option>
                                    <option value="std5">Standard 5</option>
                                    <option value="std6">Standard 6</option>
                                    <option value="std7">Standard 7</option>
                                </optgroup>
                                <optgroup label="Secondary (O-Level)">
                                    <option value="form1">Form 1</option>
                                    <option value="form2">Form 2</option>
                                    <option value="form3">Form 3</option>
                                    <option value="form4">Form 4</option>
                                </optgroup>
                                <optgroup label="Advanced (A-Level)">
                                    <option value="form5">Form 5</option>
                                    <option value="form6">Form 6</option>
                                </optgroup>
                            </select>
                            <p v-if="form.errors.class_form" class="mt-1 text-sm text-red-600">{{ form.errors.class_form }}</p>
                        </div>
                    </div>
                </div>

                <!-- Schedule & Duration -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedule & Duration</h2>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-1">Exam Date *</label>
                            <input
                                v-model="form.exam_date"
                                type="date"
                                id="exam_date"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            />
                            <p v-if="form.errors.exam_date" class="mt-1 text-sm text-red-600">{{ form.errors.exam_date }}</p>
                        </div>

                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                            <input
                                v-model="form.start_time"
                                type="time"
                                id="start_time"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            />
                            <p v-if="form.errors.start_time" class="mt-1 text-sm text-red-600">{{ form.errors.start_time }}</p>
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                            <input
                                v-model="form.end_time"
                                type="time"
                                id="end_time"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            />
                            <p v-if="form.errors.end_time" class="mt-1 text-sm text-red-600">{{ form.errors.end_time }}</p>
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                            <input
                                v-model="form.duration"
                                type="number"
                                id="duration"
                                required
                                min="1"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="180"
                            />
                            <p v-if="form.errors.duration" class="mt-1 text-sm text-red-600">{{ form.errors.duration }}</p>
                        </div>
                    </div>
                </div>

                <!-- Marks & Grading -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Marks & Grading</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-1">Total Marks *</label>
                            <input
                                v-model="form.total_marks"
                                type="number"
                                id="total_marks"
                                required
                                min="1"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="100"
                            />
                            <p v-if="form.errors.total_marks" class="mt-1 text-sm text-red-600">{{ form.errors.total_marks }}</p>
                        </div>

                        <div>
                            <label for="pass_marks" class="block text-sm font-medium text-gray-700 mb-1">Pass Marks *</label>
                            <input
                                v-model="form.pass_marks"
                                type="number"
                                id="pass_marks"
                                required
                                min="1"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="40"
                            />
                            <p v-if="form.errors.pass_marks" class="mt-1 text-sm text-red-600">{{ form.errors.pass_marks }}</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea
                                v-model="form.description"
                                id="description"
                                rows="3"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="Brief description of the exam..."
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700 mb-1">Exam Instructions</label>
                            <textarea
                                v-model="form.instructions"
                                id="instructions"
                                rows="4"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="Instructions for candidates..."
                            ></textarea>
                            <p v-if="form.errors.instructions" class="mt-1 text-sm text-red-600">{{ form.errors.instructions }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('emas.exams.index')" class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors disabled:opacity-50"
                    >
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        {{ form.processing ? 'Creating...' : 'Create Exam' }}
                    </button>
                </div>
            </form>
        </div>
    </EmasLayout>
</template>

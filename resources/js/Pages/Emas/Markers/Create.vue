<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    marker_code: '',
    subjects: [],
    district: '',
    region: '',
    scope: 'district',
    qualifications: '',
    notes: '',
});

const availableSubjects = [
    'Mathematics', 'English', 'Kiswahili', 'Physics', 'Chemistry', 'Biology',
    'Geography', 'History', 'Civics', 'Commerce', 'Book Keeping', 'Computer Studies'
];

const tanzaniaRegions = [
    'Arusha', 'Dar es Salaam', 'Dodoma', 'Geita', 'Iringa', 'Kagera', 'Katavi',
    'Kigoma', 'Kilimanjaro', 'Lindi', 'Manyara', 'Mara', 'Mbeya', 'Morogoro',
    'Mtwara', 'Mwanza', 'Njombe', 'Pemba Kaskazini', 'Pemba Kusini', 'Pwani',
    'Rukwa', 'Ruvuma', 'Shinyanga', 'Simiyu', 'Singida', 'Songwe', 'Tabora',
    'Tanga', 'Unguja Kaskazini', 'Unguja Kusini'
];

const toggleSubject = (subject) => {
    const index = form.subjects.indexOf(subject);
    if (index > -1) {
        form.subjects.splice(index, 1);
    } else {
        form.subjects.push(subject);
    }
};

const submit = () => {
    form.post(route('emas.markers.store'));
};
</script>

<template>
    <Head title="Add New Marker - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Add New Marker</h1>
                    <p class="mt-1 text-sm text-gray-600">Register a new exam marker</p>
                </div>
                <Link :href="route('emas.markers.index')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </Link>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Personal Information -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input v-model="form.name" type="text" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marker Code *</label>
                            <input v-model="form.marker_code" type="text" required placeholder="e.g., MRK-2025-001" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <p v-if="form.errors.marker_code" class="mt-1 text-sm text-red-600">{{ form.errors.marker_code }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input v-model="form.email" type="email" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input v-model="form.phone" type="tel" required placeholder="+255..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Subjects -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Subjects to Mark *</h2>
                    <p class="text-sm text-gray-600 mb-4">Select all subjects this marker is qualified to mark</p>
                    <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        <div v-for="subject in availableSubjects" :key="subject" 
                             @click="toggleSubject(subject)"
                             class="flex items-center gap-3 rounded-lg border-2 p-4 cursor-pointer transition-all"
                             :class="form.subjects.includes(subject) ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300'">
                            <div class="flex h-5 w-5 items-center justify-center rounded border-2"
                                 :class="form.subjects.includes(subject) ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300'">
                                <svg v-if="form.subjects.includes(subject)" class="h-3 w-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium" :class="form.subjects.includes(subject) ? 'text-emerald-900' : 'text-gray-700'">
                                {{ subject }}
                            </span>
                        </div>
                    </div>
                    <p v-if="form.errors.subjects" class="mt-2 text-sm text-red-600">{{ form.errors.subjects }}</p>
                </div>

                <!-- Location & Scope -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Location & Marking Scope</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Region *</label>
                            <select v-model="form.region" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <option value="">Select Region</option>
                                <option v-for="region in tanzaniaRegions" :key="region" :value="region">{{ region }}</option>
                            </select>
                            <p v-if="form.errors.region" class="mt-1 text-sm text-red-600">{{ form.errors.region }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marking Scope *</label>
                            <select v-model="form.scope" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <option value="district">District Level (Specific District)</option>
                                <option value="region">Region Level (All Districts in Region)</option>
                            </select>
                            <p v-if="form.errors.scope" class="mt-1 text-sm text-red-600">{{ form.errors.scope }}</p>
                        </div>

                        <div v-if="form.scope === 'district'" class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Specific District</label>
                            <input v-model="form.district" type="text" placeholder="Enter district name" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <p class="mt-1 text-sm text-gray-500">Leave empty if marking for entire region</p>
                            <p v-if="form.errors.district" class="mt-1 text-sm text-red-600">{{ form.errors.district }}</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Qualifications</label>
                            <textarea v-model="form.qualifications" rows="3" placeholder="Educational background, certifications, experience..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea v-model="form.notes" rows="2" placeholder="Any additional notes..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4">
                    <Link :href="route('emas.markers.index')" class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors disabled:opacity-50">
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Add Marker</span>
                    </button>
                </div>
            </form>
        </div>
    </EmasLayout>
</template>

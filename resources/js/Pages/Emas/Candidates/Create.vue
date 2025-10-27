<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const form = useForm({
    first_name: '',
    middle_name: '',
    last_name: '',
    date_of_birth: '',
    gender: '',
    registration_number: '',
    exam_center_code: '',
    level: '',
    class_form: '',
    phone: '',
    email: '',
    guardian_name: '',
    guardian_phone: '',
    address: '',
    district: '',
    region: '',
    special_needs: '',
    photo: null
});

const submit = () => {
    form.post(route('emas.candidates.store'));
};
</script>

<template>
    <Head title="Register Candidate - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Register New Candidate</h1>
                    <p class="mt-1 text-sm text-gray-600">Add a new candidate to the examination system</p>
                </div>
                <Link :href="route('emas.candidates.index')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Candidates
                </Link>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Personal Information -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input
                                v-model="form.first_name"
                                type="text"
                                id="first_name"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="John"
                            />
                            <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">{{ form.errors.first_name }}</p>
                        </div>

                        <div>
                            <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input
                                v-model="form.middle_name"
                                type="text"
                                id="middle_name"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="Michael"
                            />
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input
                                v-model="form.last_name"
                                type="text"
                                id="last_name"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="Doe"
                            />
                            <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">{{ form.errors.last_name }}</p>
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label>
                            <input
                                v-model="form.date_of_birth"
                                type="date"
                                id="date_of_birth"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            />
                            <p v-if="form.errors.date_of_birth" class="mt-1 text-sm text-red-600">{{ form.errors.date_of_birth }}</p>
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select
                                v-model="form.gender"
                                id="gender"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            >
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <p v-if="form.errors.gender" class="mt-1 text-sm text-red-600">{{ form.errors.gender }}</p>
                        </div>

                        <div>
                            <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">Registration Number *</label>
                            <input
                                v-model="form.registration_number"
                                type="text"
                                id="registration_number"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="CND-2025-001234"
                            />
                            <p v-if="form.errors.registration_number" class="mt-1 text-sm text-red-600">{{ form.errors.registration_number }}</p>
                        </div>
                    </div>
                </div>

                <!-- Education Details -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Education Details</h2>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="exam_center_code" class="block text-sm font-medium text-gray-700 mb-1">Exam Center *</label>
                            <select
                                v-model="form.exam_center_code"
                                id="exam_center_code"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            >
                                <option value="">Select Center</option>
                                <option value="EC001">EC001 - Dar es Salaam Central</option>
                                <option value="EC002">EC002 - Arusha Regional</option>
                                <option value="EC003">EC003 - Mwanza District</option>
                                <option value="EC004">EC004 - Dodoma Municipal</option>
                                <option value="EC005">EC005 - Mbeya Highland</option>
                            </select>
                            <p v-if="form.errors.exam_center_code" class="mt-1 text-sm text-red-600">{{ form.errors.exam_center_code }}</p>
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

                <!-- Contact Information -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                id="phone"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="+255 712 345 678"
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input
                                v-model="form.email"
                                type="email"
                                id="email"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="candidate@email.com"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label for="guardian_name" class="block text-sm font-medium text-gray-700 mb-1">Guardian Name *</label>
                            <input
                                v-model="form.guardian_name"
                                type="text"
                                id="guardian_name"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="Parent/Guardian Full Name"
                            />
                            <p v-if="form.errors.guardian_name" class="mt-1 text-sm text-red-600">{{ form.errors.guardian_name }}</p>
                        </div>

                        <div>
                            <label for="guardian_phone" class="block text-sm font-medium text-gray-700 mb-1">Guardian Phone *</label>
                            <input
                                v-model="form.guardian_phone"
                                type="tel"
                                id="guardian_phone"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="+255 713 456 789"
                            />
                            <p v-if="form.errors.guardian_phone" class="mt-1 text-sm text-red-600">{{ form.errors.guardian_phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Location Details -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Location Details</h2>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="lg:col-span-3">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Physical Address *</label>
                            <textarea
                                v-model="form.address"
                                id="address"
                                required
                                rows="2"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="Street, Ward, Village..."
                            ></textarea>
                            <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                        </div>

                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700 mb-1">District *</label>
                            <input
                                v-model="form.district"
                                type="text"
                                id="district"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="e.g., Kinondoni"
                            />
                            <p v-if="form.errors.district" class="mt-1 text-sm text-red-600">{{ form.errors.district }}</p>
                        </div>

                        <div>
                            <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Region *</label>
                            <select
                                v-model="form.region"
                                id="region"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            >
                                <option value="">Select Region</option>
                                <option value="arusha">Arusha</option>
                                <option value="dar_es_salaam">Dar es Salaam</option>
                                <option value="dodoma">Dodoma</option>
                                <option value="geita">Geita</option>
                                <option value="iringa">Iringa</option>
                                <option value="kagera">Kagera</option>
                                <option value="katavi">Katavi</option>
                                <option value="kigoma">Kigoma</option>
                                <option value="kilimanjaro">Kilimanjaro</option>
                                <option value="lindi">Lindi</option>
                                <option value="manyara">Manyara</option>
                                <option value="mara">Mara</option>
                                <option value="mbeya">Mbeya</option>
                                <option value="morogoro">Morogoro</option>
                                <option value="mtwara">Mtwara</option>
                                <option value="mwanza">Mwanza</option>
                                <option value="njombe">Njombe</option>
                                <option value="pwani">Pwani</option>
                                <option value="rukwa">Rukwa</option>
                                <option value="ruvuma">Ruvuma</option>
                                <option value="shinyanga">Shinyanga</option>
                                <option value="simiyu">Simiyu</option>
                                <option value="singida">Singida</option>
                                <option value="songwe">Songwe</option>
                                <option value="tabora">Tabora</option>
                                <option value="tanga">Tanga</option>
                            </select>
                            <p v-if="form.errors.region" class="mt-1 text-sm text-red-600">{{ form.errors.region }}</p>
                        </div>

                        <div>
                            <label for="special_needs" class="block text-sm font-medium text-gray-700 mb-1">Special Needs</label>
                            <input
                                v-model="form.special_needs"
                                type="text"
                                id="special_needs"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                placeholder="e.g., Visual impairment, Extra time"
                            />
                            <p v-if="form.errors.special_needs" class="mt-1 text-sm text-red-600">{{ form.errors.special_needs }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('emas.candidates.index')" class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
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
                        {{ form.processing ? 'Registering...' : 'Register Candidate' }}
                    </button>
                </div>
            </form>
        </div>
    </EmasLayout>
</template>

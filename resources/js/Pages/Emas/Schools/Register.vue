<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const form = useForm({
    centre_number: '',
    centre_name: '',
    region: '',
    council: '',
    ward: '',
    ownership: 'GOVERNMENT',
    female_students: 0,
    male_students: 0,
    contact_person: '',
    phone: '',
    email: '',
    address: '',
});

const submit = () => {
    form.post(route('emas.schools.store'));
};
</script>

<template>
    <Head title="Register School" />
    
    <EmasLayout>
        <div class="mx-auto max-w-4xl space-y-6">
            <!-- Header -->
            <div class="rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white shadow-lg">
                <h1 class="text-2xl font-bold text-center">Register New School</h1>
                <p class="mt-1 text-center text-emerald-100">EMAS - Examination Management System</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="rounded-lg bg-white p-6 shadow-md">
                <div class="space-y-6">
                    <!-- School Information -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">School Information</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Centre Number *</label>
                                <input 
                                    v-model="form.centre_number" 
                                    type="text" 
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="e.g., S4097"
                                />
                                <div v-if="form.errors.centre_number" class="mt-1 text-sm text-red-600">{{ form.errors.centre_number }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Centre Name *</label>
                                <input 
                                    v-model="form.centre_name" 
                                    type="text" 
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="e.g., BUGUZA"
                                />
                                <div v-if="form.errors.centre_name" class="mt-1 text-sm text-red-600">{{ form.errors.centre_name }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Region *</label>
                                <input 
                                    v-model="form.region" 
                                    type="text" 
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="e.g., MWANZA"
                                />
                                <div v-if="form.errors.region" class="mt-1 text-sm text-red-600">{{ form.errors.region }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Council *</label>
                                <input 
                                    v-model="form.council" 
                                    type="text" 
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="e.g., UKEREWE DC"
                                />
                                <div v-if="form.errors.council" class="mt-1 text-sm text-red-600">{{ form.errors.council }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ward</label>
                                <input 
                                    v-model="form.ward" 
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="e.g., KAGUNGULI"
                                />
                                <div v-if="form.errors.ward" class="mt-1 text-sm text-red-600">{{ form.errors.ward }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ownership *</label>
                                <select 
                                    v-model="form.ownership" 
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="GOVERNMENT">Government</option>
                                    <option value="PRIVATE">Private</option>
                                </select>
                                <div v-if="form.errors.ownership" class="mt-1 text-sm text-red-600">{{ form.errors.ownership }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Numbers -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Student Numbers</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Female Students *</label>
                                <input 
                                    v-model.number="form.female_students" 
                                    type="number" 
                                    min="0"
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                />
                                <div v-if="form.errors.female_students" class="mt-1 text-sm text-red-600">{{ form.errors.female_students }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Male Students *</label>
                                <input 
                                    v-model.number="form.male_students" 
                                    type="number" 
                                    min="0"
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                />
                                <div v-if="form.errors.male_students" class="mt-1 text-sm text-red-600">{{ form.errors.male_students }}</div>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            Total Students: <span class="font-semibold text-emerald-600">{{ form.female_students + form.male_students }}</span>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information (Optional)</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                                <input 
                                    v-model="form.contact_person" 
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input 
                                    v-model="form.phone" 
                                    type="tel"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="e.g., +255712345678"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input 
                                    v-model="form.email" 
                                    type="email"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea 
                                    v-model="form.address"
                                    rows="3"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <button 
                            type="button"
                            @click="$inertia.visit(route('emas.schools.index'))"
                            class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-emerald-600 px-6 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Registering...</span>
                            <span v-else>Register School</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </EmasLayout>
</template>

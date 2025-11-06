<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const props = defineProps({
    centers: Array,
});

const form = useForm({
    full_name: '',
    email: '',
    phone: '',
    id_number: '',
    address: '',
    centers: [],
});

const submit = () => {
    form.post(route('emas.coordinators.store'));
};
</script>

<template>
    <Head title="Add Coordinator" />
    
    <EmasLayout>
        <div class="mx-auto max-w-4xl space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Add New Coordinator</h1>
                    <p class="mt-1 text-sm text-gray-600">Create a new center coordinator account</p>
                </div>
                <Link :href="route('emas.coordinators.index')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </Link>
            </div>

            <!-- Info Box -->
            <div class="rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4">
                <div class="flex gap-3">
                    <svg class="h-6 w-6 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-blue-900">Auto-Generated Credentials</h3>
                        <p class="mt-1 text-sm text-blue-800">
                            Username will be generated from the full name (e.g., "John Doe" → "johnd"). 
                            Default password: <span class="font-mono font-bold">emas</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6 rounded-lg bg-white p-8 shadow-sm">
                <!-- Personal Information -->
                <div>
                    <h2 class="mb-4 text-lg font-bold text-gray-900">Personal Information</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="form.full_name"
                                type="text"
                                required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="John Doe"
                            />
                            <div v-if="form.errors.full_name" class="mt-1 text-sm text-red-600">{{ form.errors.full_name }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                            <input 
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="john@example.com"
                            />
                            <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Phone</label>
                            <input 
                                v-model="form.phone"
                                type="tel"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="+255 XXX XXX XXX"
                            />
                            <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">ID Number</label>
                            <input 
                                v-model="form.id_number"
                                type="text"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="National ID / Passport"
                            />
                            <div v-if="form.errors.id_number" class="mt-1 text-sm text-red-600">{{ form.errors.id_number }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Address</label>
                            <textarea 
                                v-model="form.address"
                                rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Full address"
                            ></textarea>
                            <div v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</div>
                        </div>
                    </div>
                </div>

                <!-- Center Assignment -->
                <div>
                    <h2 class="mb-4 text-lg font-bold text-gray-900">Center Assignment</h2>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Assign Centers <span class="text-red-500">*</span>
                        </label>
                        <p class="mb-3 text-xs text-gray-600">
                            Select one or more centers. The first selected center will be the primary center.
                        </p>
                        <div class="max-h-64 space-y-2 overflow-y-auto rounded-lg border border-gray-300 p-4">
                            <label 
                                v-for="center in centers" 
                                :key="center.id"
                                class="flex items-start gap-3 rounded-lg p-3 hover:bg-gray-50 cursor-pointer"
                            >
                                <input 
                                    type="checkbox"
                                    :value="center.id"
                                    v-model="form.centers"
                                    class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                />
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ center.center_name }}</div>
                                    <div class="text-sm text-gray-500">Code: {{ center.center_code }}</div>
                                </div>
                                <span 
                                    v-if="form.centers[0] === center.id"
                                    class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800"
                                >
                                    Primary
                                </span>
                            </label>
                        </div>
                        <div v-if="form.errors.centers" class="mt-1 text-sm text-red-600">{{ form.errors.centers }}</div>
                        <p v-if="form.centers.length > 0" class="mt-2 text-xs text-green-600">
                            ✓ {{ form.centers.length }} center(s) selected
                        </p>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-4 border-t pt-6">
                    <Link 
                        :href="route('emas.coordinators.index')"
                        class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </Link>
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-50"
                    >
                        <span v-if="!form.processing">Create Coordinator</span>
                        <span v-else>Creating...</span>
                    </button>
                </div>
            </form>
        </div>
    </EmasLayout>
</template>

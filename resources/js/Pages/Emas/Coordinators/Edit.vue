<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const props = defineProps({
    coordinator: Object,
    centers: Array,
});

const form = useForm({
    full_name: props.coordinator.full_name,
    email: props.coordinator.email || '',
    phone: props.coordinator.phone || '',
    id_number: props.coordinator.id_number || '',
    address: props.coordinator.address || '',
    status: props.coordinator.status,
    centers: props.coordinator.centers.map(c => c.id),
});

const submit = () => {
    form.put(route('emas.coordinators.update', props.coordinator.id));
};
</script>

<template>
    <Head title="Edit Coordinator" />
    
    <EmasLayout>
        <div class="mx-auto max-w-4xl space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Coordinator</h1>
                    <p class="mt-1 text-sm text-gray-600">Update coordinator information</p>
                </div>
                <Link :href="route('emas.coordinators.index')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </Link>
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
                            />
                            <div v-if="form.errors.full_name" class="mt-1 text-sm text-red-600">{{ form.errors.full_name }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Username</label>
                            <input 
                                :value="coordinator.username"
                                type="text"
                                disabled
                                class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm cursor-not-allowed"
                            />
                            <p class="mt-1 text-xs text-gray-500">Username cannot be changed</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                            <select 
                                v-model="form.status"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                            <input 
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            />
                            <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Phone</label>
                            <input 
                                v-model="form.phone"
                                type="tel"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            />
                            <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">ID Number</label>
                            <input 
                                v-model="form.id_number"
                                type="text"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            />
                            <div v-if="form.errors.id_number" class="mt-1 text-sm text-red-600">{{ form.errors.id_number }}</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Address</label>
                            <textarea 
                                v-model="form.address"
                                rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
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
                        <span v-if="!form.processing">Update Coordinator</span>
                        <span v-else>Updating...</span>
                    </button>
                </div>
            </form>
        </div>
    </EmasLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref } from 'vue';

defineProps({
    markers: Object,
});

const searchQuery = ref('');
const selectedRegion = ref('');
const selectedScope = ref('');
</script>

<template>
    <Head title="Markers Management - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Markers Management</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage exam markers and their assignments</p>
                </div>
                <Link :href="route('emas.markers.create')" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Marker
                </Link>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Markers</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ markers.total || 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100">
                            <svg class="h-6 w-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Markers</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-600">{{ markers.data?.filter(m => m.status === 'active').length || 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                            <svg class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">District Scope</p>
                            <p class="mt-2 text-3xl font-bold text-blue-600">{{ markers.data?.filter(m => m.scope === 'district').length || 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Region Scope</p>
                            <p class="mt-2 text-3xl font-bold text-purple-600">{{ markers.data?.filter(m => m.scope === 'region').length || 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                            <svg class="h-6 w-6 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input v-model="searchQuery" type="text" placeholder="Search by name, code, email..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                        <select v-model="selectedRegion" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="">All Regions</option>
                            <option value="Dar es Salaam">Dar es Salaam</option>
                            <option value="Arusha">Arusha</option>
                            <option value="Mwanza">Mwanza</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scope</label>
                        <select v-model="selectedScope" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="">All Scopes</option>
                            <option value="district">District</option>
                            <option value="region">Region</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Markers Table -->
            <div class="rounded-lg bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Marker</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Subjects</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Scope</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Stats</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="marker in markers.data" :key="marker.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ marker.name }}</p>
                                        <p class="text-sm text-gray-500">{{ marker.email }}</p>
                                        <p class="text-sm text-gray-500">{{ marker.phone }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ marker.marker_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="subject in marker.subjects" :key="subject" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800">
                                            {{ subject }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="marker.scope === 'district'" class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                                        District
                                    </span>
                                    <span v-else class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-purple-100 text-purple-800">
                                        Region
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ marker.region }}</p>
                                    <p v-if="marker.district" class="text-sm text-gray-500">{{ marker.district }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ marker.exams_marked }} exams</p>
                                    <p class="text-sm text-gray-500">{{ marker.papers_marked }} papers</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="marker.status === 'active'" class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <span v-else-if="marker.status === 'inactive'" class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                    <span v-else class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">
                                        Suspended
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('emas.markers.show', marker.id)" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 transition-colors" title="View">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </Link>
                                        <Link :href="route('emas.markers.edit', marker.id)" class="rounded-lg p-2 text-emerald-600 hover:bg-emerald-50 transition-colors" title="Edit">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

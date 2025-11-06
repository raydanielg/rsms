<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const props = defineProps({
    schools: Array,
    stats: Object,
    filters: Object,
});

const page = usePage();
const selectedRegion = ref(props.filters.selected_region || '');
const selectedCouncil = ref(props.filters.selected_council || '');
const selectedOwnership = ref(props.filters.selected_ownership || '');
const councils = ref(props.filters.councils || []);
const loading = ref(false);
const syncing = ref(false);

// Sync centers manually
const syncCenters = () => {
    if (confirm('Sync all EMAS Centers to Schools Registration?')) {
        syncing.value = true;
        router.post(route('emas.schools.sync-centers'), {}, {
            preserveScroll: true,
            onFinish: () => {
                syncing.value = false;
            }
        });
    }
};

// Live update when filters change
watch([selectedRegion, selectedCouncil, selectedOwnership], async () => {
    loading.value = true;
    
    try {
        const params = new URLSearchParams();
        if (selectedRegion.value) params.append('region', selectedRegion.value);
        if (selectedCouncil.value) params.append('council', selectedCouncil.value);
        if (selectedOwnership.value) params.append('ownership', selectedOwnership.value);

        const response = await fetch(route('emas.schools.data') + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (response.ok) {
            const data = await response.json();
            // Update page data without full reload
            router.reload({ 
                only: ['schools', 'stats', 'filters'],
                data: {
                    region: selectedRegion.value,
                    council: selectedCouncil.value,
                    ownership: selectedOwnership.value,
                }
            });
        }
    } catch (error) {
        console.error('Failed to load data:', error);
    } finally {
        loading.value = false;
    }
});

// When region changes, update councils
watch(selectedRegion, async (newRegion) => {
    selectedCouncil.value = '';
    if (newRegion) {
        const params = new URLSearchParams({ region: newRegion });
        const response = await fetch(route('emas.schools.data') + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (response.ok) {
            const data = await response.json();
            councils.value = data.councils;
        }
    }
});
</script>

<template>
    <Head title="Schools Registration" />
    
    <EmasLayout>
        <div class="space-y-6">
            <!-- Flash Messages -->
            <div v-if="page.props.flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 shadow-md">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-emerald-800 font-medium">{{ page.props.flash.success }}</p>
                </div>
            </div>

            <!-- Header -->
            <div class="rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white shadow-lg">
                <div class="text-center">
                    <h1 class="text-2xl font-bold">THE PRESIDENT'S OFFICE</h1>
                    <h2 class="text-lg font-semibold">REGIONAL ADMINISTRATION AND LOCAL GOVERNMENT</h2>
                    <h3 class="text-xl font-bold text-yellow-300 mt-2">{{ selectedRegion || 'ALL REGIONS' }}</h3>
                    <p class="mt-1 text-emerald-100">REGIONAL FORM TWO MOCK EXAMINATION RESULTS, AUGUST, 2025</p>
                    <p class="mt-2 text-lg font-semibold border-t border-emerald-400 pt-2">SCHOOLS REGISTRATION</p>
                </div>
            </div>

            <!-- Info & Action Bar -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-blue-800">
                        <span class="font-semibold">Auto-Sync Enabled:</span> EMAS Centers are automatically synced to schools
                    </p>
                </div>
                <button 
                    @click="syncCenters"
                    :disabled="syncing"
                    class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md transition-all"
                >
                    <svg v-if="!syncing" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <svg v-else class="h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span v-if="!syncing">Sync Now</span>
                    <span v-else>Syncing...</span>
                </button>
            </div>

            <!-- Filters -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Filter Schools</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Region (Mkoa)</label>
                        <select 
                            v-model="selectedRegion" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">All Regions</option>
                            <option v-for="region in filters.regions" :key="region" :value="region">
                                {{ region }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Council (Wilaya)</label>
                        <select 
                            v-model="selectedCouncil" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            :disabled="!selectedRegion"
                        >
                            <option value="">All Councils</option>
                            <option v-for="council in councils" :key="council" :value="council">
                                {{ council }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Ownership</label>
                        <select 
                            v-model="selectedOwnership" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">All Types</option>
                            <option value="GOVERNMENT">Government</option>
                            <option value="PRIVATE">Private</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border-2 border-emerald-200 bg-white p-4 shadow-md">
                    <div class="text-sm font-medium text-gray-600">NUMBER OF SCHOOLS IN DISTRICT</div>
                    <div class="mt-2 text-3xl font-bold text-emerald-700">{{ stats.total_schools }}</div>
                </div>
                <div class="rounded-lg border-2 border-blue-200 bg-white p-4 shadow-md">
                    <div class="text-sm font-medium text-gray-600">GOVT SCHOOLS</div>
                    <div class="mt-2 text-3xl font-bold text-blue-700">{{ stats.govt_schools }}</div>
                </div>
                <div class="rounded-lg border-2 border-purple-200 bg-white p-4 shadow-md">
                    <div class="text-sm font-medium text-gray-600">PRIVATE SCHOOLS</div>
                    <div class="mt-2 text-3xl font-bold text-purple-700">{{ stats.private_schools }}</div>
                </div>
                <div class="rounded-lg border-2 border-orange-200 bg-white p-4 shadow-md">
                    <div class="text-sm font-medium text-gray-600">TOTAL</div>
                    <div class="mt-2 text-3xl font-bold text-orange-700">{{ stats.total_schools }}</div>
                </div>
            </div>

            <!-- Total Candidates Card -->
            <div class="rounded-lg border-2 border-teal-200 bg-white p-6 shadow-md">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">TOTAL CANDIDATES REGISTERED IN DISTRICT</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600">SEX</div>
                        <div class="mt-2 flex justify-center gap-8">
                            <div>
                                <div class="text-xs text-gray-500">F</div>
                                <div class="text-2xl font-bold text-pink-600">{{ stats.total_female }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">M</div>
                                <div class="text-2xl font-bold text-blue-600">{{ stats.total_male }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600">GOVERNMENT</div>
                        <div class="mt-2 text-2xl font-bold text-emerald-600">{{ stats.total_female + stats.total_male }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600">TOTAL</div>
                        <div class="mt-2 text-2xl font-bold text-teal-700">{{ stats.total_students }}</div>
                    </div>
                </div>
            </div>

            <!-- Schools Table -->
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-md">
                <div v-if="loading" class="p-4 text-center">
                    <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-emerald-600 border-r-transparent"></div>
                    <p class="mt-2 text-sm text-gray-600">Loading...</p>
                </div>
                
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-emerald-600 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">S/NO.</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">COUNCIL</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">WARD</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">CENTRE NUMBER</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">CENTRE NAME</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">OWNERSHIP</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase">FEMALE</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase">MALE</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase">STUDENTS REGISTERED</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="(school, index) in schools" :key="school.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">{{ index + 1 }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ school.council }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ school.ward || '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ school.centre_number }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ school.centre_name }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span 
                                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                        :class="school.ownership === 'GOVERNMENT' ? 'bg-emerald-100 text-emerald-800' : 'bg-purple-100 text-purple-800'"
                                    >
                                        {{ school.ownership }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ school.female_students }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ school.male_students }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold text-blue-600">{{ school.total_students }}</td>
                            </tr>
                            <tr v-if="schools.length === 0">
                                <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No schools found. Try adjusting your filters.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="schools.length > 0" class="bg-gray-50 font-bold">
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right text-sm uppercase text-gray-900">TOTAL CANDIDATES REGISTERED IN DISTRICT</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900">{{ stats.total_female }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900">{{ stats.total_male }}</td>
                                <td class="px-4 py-3 text-right text-sm text-blue-700">{{ stats.total_students }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

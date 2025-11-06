<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    centers: Object,
    stats: Object,
    usedRegions: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const selectedRegion = ref(props.filters?.region || '');

// Watch for filter changes and update URL
watch([searchQuery, selectedRegion], () => {
    router.get(route('emas.results.index'), {
        search: searchQuery.value,
        region: selectedRegion.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}, { debounce: 500 });

const formatNumber = (num) => new Intl.NumberFormat('sw-TZ').format(num);

const clearFilters = () => {
    searchQuery.value = '';
    selectedRegion.value = '';
};
</script>

<template>
    <Head title="Matokeo ya Shule - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-black text-gray-900">Matokeo ya Shule</h1>
                <p class="mt-2 text-sm text-gray-600">Tazama matokeo ya mitihani kutoka shule mbalimbali</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-emerald-100">Shule</p>
                            <p class="mt-2 text-4xl font-black text-white">{{ formatNumber(stats.total_centers) }}</p>
                        </div>
                        <div class="rounded-full bg-white/20 p-3">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-blue-100">Matokeo</p>
                            <p class="mt-2 text-4xl font-black text-white">{{ formatNumber(stats.total_results) }}</p>
                        </div>
                        <div class="rounded-full bg-white/20 p-3">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-purple-100">Wanafunzi</p>
                            <p class="mt-2 text-4xl font-black text-white">{{ formatNumber(stats.total_candidates) }}</p>
                        </div>
                        <div class="rounded-full bg-white/20 p-3">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-amber-100">Mikoa</p>
                            <p class="mt-2 text-4xl font-black text-white">{{ formatNumber(stats.regions_count) }}</p>
                        </div>
                        <div class="rounded-full bg-white/20 p-3">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-lg bg-white p-5 shadow-sm border border-gray-200">
                <div class="flex flex-col gap-4 md:flex-row md:items-center">
                    <div class="flex-1">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Tafuta shule kwa jina, namba au wilaya..."
                                class="w-full rounded-lg border-2 border-gray-300 py-2.5 pl-11 pr-4 text-sm font-medium focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            />
                            <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <select 
                            v-model="selectedRegion" 
                            class="rounded-lg border-2 border-gray-300 px-4 py-2.5 text-sm font-semibold focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        >
                            <option value="">Mikoa Yote</option>
                            <option v-for="region in usedRegions" :key="region" :value="region">
                                {{ region }}
                            </option>
                        </select>
                        <button 
                            v-if="searchQuery || selectedRegion"
                            @click="clearFilters" 
                            class="rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-200 transition-colors"
                        >
                            Futa Chujio
                        </button>
                    </div>
                </div>
            </div>

            <!-- Schools Grid -->
            <div v-if="centers.data && centers.data.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link 
                    v-for="center in centers.data" 
                    :key="center.id"
                    :href="route('emas.results.school', center.id)"
                    class="group rounded-xl bg-white p-6 shadow-md border-2 border-gray-200 hover:border-emerald-500 hover:shadow-xl transition-all duration-200"
                >
                    <!-- Center Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-black text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2">
                                {{ center.center_name }}
                            </h3>
                            <p class="mt-1 text-sm font-bold text-emerald-600">{{ center.center_code }}</p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-2 group-hover:bg-emerald-500 transition-colors">
                            <svg class="h-6 w-6 text-emerald-600 group-hover:text-white transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="flex items-center gap-2 mb-4 text-sm text-gray-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="font-semibold">{{ center.district }}, {{ center.region }}</span>
                    </div>

                    <!-- Stats -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-t border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Wanafunzi</span>
                            <span class="text-lg font-black text-gray-900">{{ formatNumber(center.candidates_count) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-t border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Matokeo</span>
                            <span class="text-lg font-black text-blue-600">{{ formatNumber(center.results_count) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-t border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Wastani</span>
                            <span class="text-lg font-black text-emerald-600">{{ center.average_score }}%</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-t border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Mitihani</span>
                            <span class="text-lg font-black text-purple-600">{{ formatNumber(center.exams_count) }}</span>
                        </div>
                    </div>

                    <!-- View Button -->
                    <div class="mt-5 pt-4 border-t-2 border-gray-100">
                        <div class="flex items-center justify-between text-emerald-600 group-hover:text-emerald-700 font-bold">
                            <span>Angalia Matokeo</span>
                            <svg class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-else class="rounded-xl bg-white p-12 shadow-sm text-center border-2 border-dashed border-gray-300">
                <div class="mx-auto w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Hakuna Shule</h3>
                <p class="text-gray-600 mb-6">Hakuna shule zilizopatikana kwa chujio ulilochagua</p>
                <button 
                    v-if="searchQuery || selectedRegion"
                    @click="clearFilters" 
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-700 transition-colors"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Futa Chujio
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="centers.data && centers.data.length > 0" class="flex items-center justify-between rounded-lg bg-white px-6 py-4 shadow-sm border border-gray-200">
                <div class="text-sm text-gray-700">
                    Kuonyesha <span class="font-bold text-gray-900">{{ centers.from }}</span> hadi <span class="font-bold text-gray-900">{{ centers.to }}</span> kati ya <span class="font-bold text-gray-900">{{ centers.total }}</span> shule
                </div>
                <div class="flex gap-2">
                    <Link 
                        v-if="centers.prev_page_url" 
                        :href="centers.prev_page_url"
                        class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors"
                        preserve-scroll
                    >
                        Nyuma
                    </Link>
                    <span v-else class="rounded-lg border-2 border-gray-200 px-4 py-2 text-sm font-bold text-gray-400 cursor-not-allowed">
                        Nyuma
                    </span>
                    <Link 
                        v-if="centers.next_page_url" 
                        :href="centers.next_page_url"
                        class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors"
                        preserve-scroll
                    >
                        Mbele
                    </Link>
                    <span v-else class="rounded-lg border-2 border-gray-200 px-4 py-2 text-sm font-bold text-gray-400 cursor-not-allowed">
                        Mbele
                    </span>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

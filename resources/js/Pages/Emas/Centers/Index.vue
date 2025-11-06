<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    centers: Object,
    stats: Object,
    allRegions: Array,
    usedRegions: Array,
    regionCounts: Object,
    filters: Object,
});

const page = usePage();
const showCredentials = ref(false);

// Check if we have coordinator credentials in flash
if (page.props.flash?.coordinator_username) {
    showCredentials.value = true;
}

const searchQuery = ref(props.filters.search || '');
const selectedRegion = ref(props.filters.region || '');
const selectedStatus = ref(props.filters.status || '');
const viewMode = ref('list'); // 'grid' or 'list'

// Simple debounce function
const debounce = (func, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func(...args), delay);
    };
};

// Debounce search
const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

watch(searchQuery, () => {
    debouncedSearch();
});

const applyFilters = () => {
    router.get(route('emas.centers.index'), {
        search: searchQuery.value,
        region: selectedRegion.value,
        status: selectedStatus.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const selectRegion = (region) => {
    selectedRegion.value = region;
    applyFilters();
};

const clearRegion = () => {
    selectedRegion.value = '';
    applyFilters();
};

const getRegionCenterCount = (region) => {
    return props.regionCounts[region] || 0;
};

const getStatusColor = (status) => {
    return status === 'active' 
        ? 'bg-emerald-100 text-emerald-800' 
        : 'bg-gray-100 text-gray-800';
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num);
};
</script>

<template>
    <Head title="Exam Centers - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Exam Centers</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage examination centers and venues</p>
                </div>
                <Link :href="route('emas.centers.create')" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Center
                </Link>
            </div>

            <!-- Coordinator Credentials Alert -->
            <div v-if="showCredentials && page.props.flash?.coordinator_username" class="rounded-lg border-l-4 border-emerald-500 bg-emerald-50 p-6 shadow-md">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-emerald-900">Center Created Successfully!</h3>
                            <p class="mt-1 text-sm text-emerald-800">{{ page.props.flash.success }}</p>
                            
                            <div class="mt-4 rounded-lg bg-white border border-emerald-200 p-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">📋 Coordinator Login Credentials</h4>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Username</label>
                                        <div class="flex items-center gap-2">
                                            <code class="block rounded bg-gray-100 px-3 py-2 text-sm font-mono font-semibold text-gray-900">{{ page.props.flash.coordinator_username }}</code>
                                            <button 
                                                @click="navigator.clipboard.writeText(page.props.flash.coordinator_username)"
                                                class="rounded p-2 hover:bg-gray-100 text-gray-600"
                                                title="Copy username"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Password</label>
                                        <div class="flex items-center gap-2">
                                            <code class="block rounded bg-gray-100 px-3 py-2 text-sm font-mono font-semibold text-gray-900">{{ page.props.flash.coordinator_password }}</code>
                                            <button 
                                                @click="navigator.clipboard.writeText(page.props.flash.coordinator_password)"
                                                class="rounded p-2 hover:bg-gray-100 text-gray-600"
                                                title="Copy password"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 text-xs text-gray-600">
                                    ⚠️ <strong>Important:</strong> Save these credentials securely. Share them with the coordinator for center management access.
                                </p>
                            </div>
                        </div>
                    </div>
                    <button 
                        @click="showCredentials = false"
                        class="flex-shrink-0 rounded-lg p-1 hover:bg-emerald-100 text-emerald-600"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Success Message (without credentials) -->
            <div v-else-if="page.props.flash?.success" class="rounded-lg border-l-4 border-emerald-500 bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-emerald-800 font-medium">{{ page.props.flash.success }}</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Jumla ya Vituo</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ formatNumber(stats.total_centers) }}</p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-3">
                            <svg class="h-8 w-8 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Vituo Vilivyopo Mtandaoni</p>
                            <p class="mt-2 text-3xl font-bold text-blue-700">{{ formatNumber(stats.active_centers) }}</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3">
                            <svg class="h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Uwezo wa Jumla</p>
                            <p class="mt-2 text-3xl font-bold text-purple-700">{{ formatNumber(stats.total_capacity) }}</p>
                        </div>
                        <div class="rounded-full bg-purple-100 p-3">
                            <svg class="h-8 w-8 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Mikoa</p>
                            <p class="mt-2 text-3xl font-bold text-amber-700">{{ formatNumber(stats.regions_count) }}</p>
                        </div>
                        <div class="rounded-full bg-amber-100 p-3">
                            <svg class="h-8 w-8 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regions Quick View -->
            <div v-if="!searchQuery && !selectedRegion && usedRegions.length > 0" class="rounded-lg bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Mikoa</h2>
                    <p class="text-sm text-gray-600">Bofya mkoa kuona vituo vyake</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-5">
                    <button
                        v-for="region in usedRegions"
                        :key="region"
                        @click="selectRegion(region)"
                        class="group rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 p-4 text-left hover:from-emerald-100 hover:to-teal-100 transition-all hover:shadow-md border-2 border-emerald-100 hover:border-emerald-400"
                    >
                        <div class="flex items-center gap-2 mb-2">
                            <div class="rounded-lg bg-emerald-500 p-1.5">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <svg class="h-4 w-4 text-emerald-600 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 mb-0.5">{{ region }}</h3>
                        <p class="text-xs text-gray-600">{{ getRegionCenterCount(region) }} vituo</p>
                    </button>
                </div>
            </div>

            <!-- Active Region Banner -->
            <div v-if="selectedRegion" class="rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-white/20 p-2">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-emerald-100">Mkoa wa</p>
                            <h2 class="text-xl font-black text-white">{{ selectedRegion }}</h2>
                        </div>
                    </div>
                    <button
                        @click="clearRegion"
                        class="rounded-lg bg-white/20 hover:bg-white/30 px-4 py-2 text-sm font-bold transition-colors flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Ondoa
                    </button>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="rounded-lg bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex-1">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Tafuta kituo kwa jina, code, au mahali..."
                                class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            />
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select 
                            v-model="selectedRegion" 
                            @change="applyFilters"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        >
                            <option value="">Mikoa Yote</option>
                            <option v-for="region in usedRegions" :key="region" :value="region">{{ region }}</option>
                        </select>
                        <select 
                            v-model="selectedStatus" 
                            @change="applyFilters"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        >
                            <option value="">Hali Zote</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Centers Grid - Only show when region is selected -->
            <div v-if="!selectedRegion && !searchQuery" class="rounded-lg bg-white p-12 text-center shadow-sm">
                <div class="mx-auto h-24 w-24 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                    <svg class="h-12 w-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Chagua Mkoa Kuanza</h3>
                <p class="text-gray-600 mb-4">Bofya mkoa hapo juu ili kuona vituo vyake vyote</p>
            </div>

            <div v-else-if="centers.data.length === 0" class="rounded-lg bg-white p-12 text-center shadow-sm">
                <div class="mx-auto h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Hakuna Vituo Vilivyopatikana</h3>
                <p class="text-gray-600 mb-4">{{ searchQuery ? 'Jaribu kubadilisha vigezo vya utafutaji' : 'Mkoa huu hauna vituo bado' }}</p>
                <Link :href="route('emas.centers.create')" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ongeza Kituo
                </Link>
            </div>

            <div v-else>
                <!-- Centers List Header -->
                <div class="rounded-lg bg-white p-4 shadow-sm mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Orodha ya Vituo</h3>
                            <p class="text-sm text-gray-600">{{ centers.total }} vituo {{ selectedRegion ? 'vya mkoa wa ' + selectedRegion : 'jumla' }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'" class="rounded-lg p-2 hover:bg-emerald-50 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </button>
                            <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'" class="rounded-lg p-2 hover:bg-emerald-50 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Centers Grid View -->
                <div v-if="viewMode === 'grid'" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="center in centers.data" :key="center.id" class="rounded-lg bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100">
                            <svg class="h-6 w-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium" :class="getStatusColor(center.status)">
                            {{ center.status === 'active' ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ center.center_name }}</h3>
                    <p class="mt-1 text-sm font-medium text-gray-500">{{ center.center_code }}</p>
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ center.district }}, {{ center.region }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Uwezo: {{ formatNumber(center.capacity) }} wanafunzi</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ center.phone }}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <Link :href="route('emas.centers.show', center.id)" class="flex-1 rounded-lg bg-emerald-50 px-3 py-2 text-center text-sm font-medium text-emerald-700 hover:bg-emerald-100 transition-colors">
                            Angalia
                        </Link>
                        <Link :href="route('emas.centers.edit', center.id)" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Hariri
                        </Link>
                    </div>
                </div>
                </div>

                <!-- Centers List View -->
                <div v-else class="rounded-lg bg-white shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kituo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilaya/Mkoa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Simu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uwezo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hali</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Vitendo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="center in centers.data" :key="center.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 flex-shrink-0">
                                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ center.center_name }}</div>
                                            <div class="text-xs text-gray-500">{{ center.center_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ center.district }}</div>
                                    <div class="text-xs text-gray-500">{{ center.region }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ center.phone }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ formatNumber(center.capacity) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium" :class="getStatusColor(center.status)">
                                        {{ center.status === 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('emas.centers.show', center.id)" class="text-emerald-600 hover:text-emerald-900 font-semibold">
                                            Angalia
                                        </Link>
                                        <span class="text-gray-300">|</span>
                                        <Link :href="route('emas.centers.edit', center.id)" class="text-gray-600 hover:text-gray-900 font-semibold">
                                            Hariri
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between rounded-lg bg-white px-4 py-3 shadow-sm mt-6">
                <div class="text-sm text-gray-700">
                    Kuonyesha <span class="font-medium">{{ centers.from }}</span> hadi <span class="font-medium">{{ centers.to }}</span> kati ya <span class="font-medium">{{ centers.total }}</span> vituo
                </div>
                <div class="flex gap-2">
                    <Link 
                        v-if="centers.prev_page_url" 
                        :href="centers.prev_page_url" 
                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        preserve-scroll
                    >
                        Nyuma
                    </Link>
                    <span v-else class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-400 cursor-not-allowed">
                        Nyuma
                    </span>
                    <Link 
                        v-if="centers.next_page_url" 
                        :href="centers.next_page_url" 
                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        preserve-scroll
                    >
                        Mbele
                    </Link>
                    <span v-else class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-400 cursor-not-allowed">
                        Mbele
                    </span>
                </div>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

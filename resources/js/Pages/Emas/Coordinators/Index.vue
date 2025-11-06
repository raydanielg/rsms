<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const props = defineProps({
    coordinators: Object,
    stats: Object,
    centers: Array,
    filters: Object,
});

const page = usePage();
const searchQuery = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const selectedCenter = ref(props.filters.center || '');
const showCredentials = ref(false);
const resetPasswordModal = ref(false);
const selectedCoordinator = ref(null);
const newPassword = ref('');

// Check if we have credentials in flash
if (page.props.flash?.coordinator_username) {
    showCredentials.value = true;
}

const debounce = (func, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func(...args), delay);
    };
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

watch(searchQuery, () => {
    debouncedSearch();
});

const applyFilters = () => {
    router.get(route('emas.coordinators.index'), {
        search: searchQuery.value,
        status: selectedStatus.value,
        center: selectedCenter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const openResetPasswordModal = (coordinator) => {
    selectedCoordinator.value = coordinator;
    newPassword.value = '';
    resetPasswordModal.value = true;
};

const resetPassword = () => {
    if (!newPassword.value || newPassword.value.length < 4) {
        alert('Password must be at least 4 characters');
        return;
    }

    router.post(route('emas.coordinators.reset-password', selectedCoordinator.value.id), {
        new_password: newPassword.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            resetPasswordModal.value = false;
            selectedCoordinator.value = null;
            newPassword.value = '';
        },
    });
};

const deleteCoordinator = (coordinator) => {
    if (confirm(`Delete coordinator "${coordinator.full_name}"? This action cannot be undone.`)) {
        router.delete(route('emas.coordinators.destroy', coordinator.id));
    }
};
</script>

<template>
    <Head title="Wasimamizi - Coordinators" />
    
    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Wasimamizi / Coordinators</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage center coordinators and their access</p>
                </div>
                <Link :href="route('emas.coordinators.create')" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Coordinator
                </Link>
            </div>

            <!-- Credentials Alert - CONGRATULATIONS! -->
            <div v-if="showCredentials && page.props.flash?.coordinator_username" class="animate-slide-down rounded-xl border-2 border-emerald-500 bg-gradient-to-r from-emerald-50 to-green-50 p-8 shadow-2xl">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-6 flex-1">
                        <!-- Success Icon with Animation -->
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <div class="absolute inset-0 rounded-full bg-emerald-400 animate-ping opacity-25"></div>
                                <div class="relative flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500 shadow-lg">
                                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1">
                            <div class="mb-2">
                                <h3 class="text-2xl font-bold text-emerald-900">🎉 Hongera! Congratulations!</h3>
                                <p class="mt-1 text-base text-emerald-800 font-medium">Msimamizi ameundwa kikamilifu! / Coordinator created successfully!</p>
                            </div>
                            
                            <!-- Credentials Card -->
                            <div class="mt-6 rounded-xl bg-white border-2 border-emerald-200 p-6 shadow-lg">
                                <div class="mb-4 flex items-center gap-2">
                                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-900">Taarifa za Kuingia / Login Credentials</h4>
                                </div>
                                
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">👤 Username / Jina la Mtumiaji</label>
                                        <div class="flex items-center gap-2">
                                            <code class="flex-1 rounded-lg bg-white border-2 border-emerald-300 px-4 py-3 text-base font-mono font-bold text-emerald-900">{{ page.props.flash.coordinator_username }}</code>
                                            <button 
                                                @click="navigator.clipboard.writeText(page.props.flash.coordinator_username)"
                                                class="rounded-lg bg-emerald-100 p-3 text-emerald-700 hover:bg-emerald-200 transition-colors"
                                                title="Copy username"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">🔑 Password / Neno la Siri</label>
                                        <div class="flex items-center gap-2">
                                            <code class="flex-1 rounded-lg bg-white border-2 border-emerald-300 px-4 py-3 text-base font-mono font-bold text-emerald-900">{{ page.props.flash.coordinator_password }}</code>
                                            <button 
                                                @click="navigator.clipboard.writeText(page.props.flash.coordinator_password)"
                                                class="rounded-lg bg-emerald-100 p-3 text-emerald-700 hover:bg-emerald-200 transition-colors"
                                                title="Copy password"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                                    <div class="flex gap-3">
                                        <svg class="h-6 w-6 flex-shrink-0 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-yellow-900">⚠️ Muhimu Sana! / Very Important!</p>
                                            <p class="mt-1 text-sm text-yellow-800">
                                                Hifadhi taarifa hizi kwa usalama na uzipe msimamizi. / 
                                                Save these credentials securely and share them with the coordinator.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Close Button -->
                    <button 
                        @click="showCredentials = false" 
                        class="flex-shrink-0 rounded-full p-2 text-emerald-600 hover:bg-emerald-100 transition-colors"
                        title="Close"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Success Message -->
            <div v-else-if="page.props.flash?.success" class="rounded-lg border-l-4 border-emerald-500 bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-emerald-800 font-medium">{{ page.props.flash.success }}</p>
                    <code v-if="page.props.flash?.new_password" class="ml-auto rounded bg-emerald-100 px-3 py-1 text-sm font-mono font-semibold text-emerald-900">
                        New Password: {{ page.props.flash.new_password }}
                    </code>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-6 sm:grid-cols-3">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Coordinators</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ stats.total }}</p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-3">
                            <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active</p>
                            <p class="mt-2 text-3xl font-bold text-green-700">{{ stats.active }}</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Inactive</p>
                            <p class="mt-2 text-3xl font-bold text-gray-700">{{ stats.inactive }}</p>
                        </div>
                        <div class="rounded-full bg-gray-100 p-3">
                            <svg class="h-8 w-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="grid gap-4 sm:grid-cols-4">
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700">Search</label>
                        <input 
                            v-model="searchQuery" 
                            type="text"
                            placeholder="Search by name, username, email, phone..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                        <select 
                            v-model="selectedStatus" 
                            @change="applyFilters"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Center</label>
                        <select 
                            v-model="selectedCenter" 
                            @change="applyFilters"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">All Centers</option>
                            <option v-for="center in centers" :key="center.id" :value="center.id">
                                {{ center.center_name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Coordinators Table -->
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Coordinator</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Centers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="coordinator in coordinators.data" :key="coordinator.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-semibold">
                                        {{ coordinator.full_name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ coordinator.full_name }}</div>
                                        <div v-if="coordinator.id_number" class="text-xs text-gray-500">ID: {{ coordinator.id_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <code class="rounded bg-gray-100 px-2 py-1 text-sm font-mono text-gray-700">{{ coordinator.username }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <div v-if="coordinator.email" class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ coordinator.email }}
                                    </div>
                                    <div v-if="coordinator.phone" class="flex items-center gap-1 mt-1">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ coordinator.phone }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span 
                                        v-for="center in coordinator.centers" 
                                        :key="center.id"
                                        :class="center.pivot.role === 'primary' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700'"
                                        class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium"
                                    >
                                        <svg v-if="center.pivot.role === 'primary'" class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ center.center_code }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span 
                                    :class="coordinator.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                >
                                    {{ coordinator.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link 
                                        :href="route('emas.coordinators.edit', coordinator.id)"
                                        class="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                        title="Edit"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </Link>
                                    <button 
                                        @click="openResetPasswordModal(coordinator)"
                                        class="rounded-lg p-2 text-purple-600 hover:bg-purple-50"
                                        title="Reset Password"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="deleteCoordinator(coordinator)"
                                        class="rounded-lg p-2 text-red-600 hover:bg-red-50"
                                        title="Delete"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="coordinators.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                No coordinators found. Add your first coordinator to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="coordinators.data.length > 0" class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ coordinators.from }} to {{ coordinators.to }} of {{ coordinators.total }} results
                        </div>
                        <div class="flex gap-2">
                            <Link 
                                v-for="link in coordinators.links" 
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                :class="link.active ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                class="rounded-lg border px-3 py-2 text-sm font-medium transition-colors"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reset Password Modal -->
        <div v-if="resetPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Reset Password</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Reset password for: <strong>{{ selectedCoordinator?.full_name }}</strong>
                </p>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input 
                        v-model="newPassword"
                        type="text"
                        placeholder="Enter new password"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    />
                    <p class="mt-1 text-xs text-gray-500">Minimum 4 characters</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button 
                        @click="resetPasswordModal = false"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="resetPassword"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
                    >
                        Reset Password
                    </button>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

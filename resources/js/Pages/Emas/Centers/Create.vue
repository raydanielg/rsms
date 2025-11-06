<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    regions: Object,
});

const form = useForm({
    center_name: '',
    center_code: '',
    address: '',
    region: '',
    district: '',
    ownership: 'GOVERNMENT',
    phone: '',
    email: '',
    capacity: '',
    coordinator_name: '',
    coordinator_phone: '',
});

// Get available districts based on selected region
const availableDistricts = computed(() => {
    if (!form.region || !props.regions[form.region]) {
        return [];
    }
    return props.regions[form.region];
});

// Reset district when region changes
watch(() => form.region, () => {
    form.district = '';
});

const submit = () => {
    form.post(route('emas.centers.store'));
};
</script>

<template>
    <Head title="Ongeza Kituo Kipya - EMAS" />

    <EmasLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Ongeza Kituo Kipya</h1>
                    <p class="mt-1 text-sm text-gray-600">Jaza taarifa za kituo cha mtihani</p>
                </div>
                <Link :href="route('emas.centers.index')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Rudi Nyuma
                </Link>
            </div>

            <!-- Credentials Info -->
            <div class="rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="h-6 w-6 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-blue-900">Automatic Coordinator Login Credentials</h3>
                        <p class="mt-1 text-sm text-blue-800">
                            A username will be automatically generated from the Coordinator Name (e.g., "John Doe" → username: "johnd").
                            The default password for all coordinators is: <span class="font-mono font-bold">emas</span>
                        </p>
                        <p class="mt-1 text-xs text-blue-700">
                            Coordinators can use these credentials to login and manage their center.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="rounded-lg bg-white p-8 shadow-sm space-y-6">
                <!-- Basic Information -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Taarifa za Msingi</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jina la Kituo <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.center_name"
                                type="text"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                placeholder="Mfano: Kituo cha Mtihani Kinondoni"
                            />
                            <div v-if="form.errors.center_name" class="mt-1 text-sm text-red-600">{{ form.errors.center_name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Code ya Kituo <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.center_code"
                                type="text"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 uppercase"
                                placeholder="Mfano: EC001"
                            />
                            <div v-if="form.errors.center_code" class="mt-1 text-sm text-red-600">{{ form.errors.center_code }}</div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Anuani <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="form.address"
                                required
                                rows="3"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                placeholder="Andika anuani kamili ya kituo"
                            ></textarea>
                            <div v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</div>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Taarifa za Mahali</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Mkoa <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.region"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            >
                                <option value="">Chagua Mkoa</option>
                                <option v-for="(districts, regionName) in regions" :key="regionName" :value="regionName">
                                    {{ regionName }}
                                </option>
                            </select>
                            <div v-if="form.errors.region" class="mt-1 text-sm text-red-600">{{ form.errors.region }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Wilaya <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.district"
                                required
                                :disabled="!form.region"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 disabled:bg-gray-100 disabled:cursor-not-allowed"
                            >
                                <option value="">{{ form.region ? 'Chagua Wilaya' : 'Chagua mkoa kwanza' }}</option>
                                <option v-for="district in availableDistricts" :key="district" :value="district">
                                    {{ district }}
                                </option>
                            </select>
                            <div v-if="form.errors.district" class="mt-1 text-sm text-red-600">{{ form.errors.district }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Umiliki / Ownership <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.ownership"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            >
                                <option value="GOVERNMENT">Serikali / Government</option>
                                <option value="PRIVATE">Binafsi / Private</option>
                            </select>
                            <div v-if="form.errors.ownership" class="mt-1 text-sm text-red-600">{{ form.errors.ownership }}</div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Taarifa za Mawasiliano</h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Namba ya Simu <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                placeholder="+255 XX XXX XXXX"
                            />
                            <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Barua Pepe
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                placeholder="email@example.com"
                            />
                            <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
                        </div>
                    </div>
                </div>

                <!-- Capacity & Coordinator -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Uwezo na Msimamizi</h2>
                    <div class="grid gap-6 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Uwezo (Wanafunzi) <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.capacity"
                                type="number"
                                required
                                min="1"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                placeholder="500"
                            />
                            <div v-if="form.errors.capacity" class="mt-1 text-sm text-red-600">{{ form.errors.capacity }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jina la Msimamizi / Coordinator Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.coordinator_name"
                                type="text"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                placeholder="John Doe"
                            />
                            <p class="mt-1 text-xs text-blue-600">
                                <svg class="inline h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Username and password will be auto-generated
                            </p>
                            <div v-if="form.errors.coordinator_name" class="mt-1 text-sm text-red-600">{{ form.errors.coordinator_name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Simu ya Msimamizi
                            </label>
                            <input
                                v-model="form.coordinator_phone"
                                type="tel"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                placeholder="+255 XX XXX XXXX"
                            />
                            <div v-if="form.errors.coordinator_phone" class="mt-1 text-sm text-red-600">{{ form.errors.coordinator_phone }}</div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t">
                    <Link :href="route('emas.centers.index')" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Ghairi
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="!form.processing">Hifadhi Kituo</span>
                        <span v-else class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Inahifadhi...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </EmasLayout>
</template>

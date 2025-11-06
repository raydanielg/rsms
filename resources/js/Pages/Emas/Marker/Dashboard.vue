<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MarkerLayout from '@/Layouts/MarkerLayout.vue';

const props = defineProps({
    assignedExams: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({})
    }
});

const getStatusBadge = (status) => {
    const badges = {
        scheduled: { color: 'bg-gray-500', text: 'Imepangwa' },
        ongoing: { color: 'bg-green-500', text: 'Inaendelea' },
        completed: { color: 'bg-blue-500', text: 'Imekamilika' },
        cancelled: { color: 'bg-red-500', text: 'Imesitishwa' }
    };
    return badges[status] || badges.scheduled;
};

const formatDate = (date) => {
    if (!date) return '--';
    return new Date(date).toLocaleDateString('sw-TZ', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Marker Dashboard - EMAS" />

    <MarkerLayout>
        <div class="space-y-6">
            <!-- Welcome Banner -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-600 via-emerald-600 to-teal-600 p-8 shadow-2xl">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-32 w-32 rounded-full bg-yellow-400 opacity-20"></div>
                <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-40 w-40 rounded-full bg-yellow-300 opacity-10"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex-1">
                        <h1 class="text-4xl font-black text-yellow-300 drop-shadow-2xl mb-2">
                            Karibu Kwenye EMAS
                        </h1>
                        <p class="text-xl font-bold text-white mb-1">Marker Panel</p>
                        <p class="text-sm text-yellow-100 font-semibold">Ingiza Alama za Mitihani Haraka na Kwa Ufanisi</p>
                    </div>
                    <div class="hidden md:flex items-center gap-4">
                        <div class="rounded-2xl bg-yellow-400 px-6 py-4 shadow-xl border-4 border-yellow-300">
                            <p class="text-xs font-bold text-green-800 uppercase">Tarehe ya Leo</p>
                            <p class="text-2xl font-black text-green-900">{{ new Date().toLocaleDateString('sw-TZ', { day: '2-digit', month: 'short' }) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards - Professional Design -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Card 1: Jumla ya Alama -->
                <div class="group relative overflow-hidden rounded-3xl bg-white p-6 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-8 border-green-500">
                    <div class="flex items-start justify-between mb-4">
                        <div class="rounded-2xl bg-green-100 p-4">
                            <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">JUMLA</span>
                    </div>
                    <div>
                        <p class="text-6xl font-black text-gray-900 mb-2">{{ stats.total_marked || 0 }}</p>
                        <p class="text-sm font-bold text-gray-600 uppercase tracking-wide">Jumla ya Alama</p>
                        <p class="text-xs text-gray-500 mt-1">Zilizowekwa jumla</p>
                    </div>
                    <div class="absolute bottom-0 right-0 opacity-5">
                        <svg class="h-32 w-32 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Card 2: Alama Leo -->
                <div class="group relative overflow-hidden rounded-3xl bg-white p-6 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-8 border-yellow-500">
                    <div class="flex items-start justify-between mb-4">
                        <div class="rounded-2xl bg-yellow-100 p-4">
                            <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-700">LEO</span>
                    </div>
                    <div>
                        <p class="text-6xl font-black text-gray-900 mb-2">{{ stats.today_marked || 0 }}</p>
                        <p class="text-sm font-bold text-gray-600 uppercase tracking-wide">Alama Leo</p>
                        <p class="text-xs text-gray-500 mt-1">Zilizowekwa leo</p>
                    </div>
                    <div class="absolute bottom-0 right-0 opacity-5">
                        <svg class="h-32 w-32 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Card 3: Mitihani -->
                <div class="group relative overflow-hidden rounded-3xl bg-white p-6 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-8 border-blue-500">
                    <div class="flex items-start justify-between mb-4">
                        <div class="rounded-2xl bg-blue-100 p-4">
                            <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">YANGU</span>
                    </div>
                    <div>
                        <p class="text-6xl font-black text-gray-900 mb-2">{{ stats.assigned_exams || 0 }}</p>
                        <p class="text-sm font-bold text-gray-600 uppercase tracking-wide">Mitihani Yako</p>
                        <p class="text-xs text-gray-500 mt-1">Iliyopangiwa</p>
                    </div>
                    <div class="absolute bottom-0 right-0 opacity-5">
                        <svg class="h-32 w-32 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>

                <!-- Card 4: Zinazongoja -->
                <div class="group relative overflow-hidden rounded-3xl bg-white p-6 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-8 border-orange-500">
                    <div class="flex items-start justify-between mb-4">
                        <div class="rounded-2xl bg-orange-100 p-4">
                            <svg class="h-10 w-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">PENDING</span>
                    </div>
                    <div>
                        <p class="text-6xl font-black text-gray-900 mb-2">{{ stats.pending_exams || 0 }}</p>
                        <p class="text-sm font-bold text-gray-600 uppercase tracking-wide">Zinazongoja</p>
                        <p class="text-xs text-gray-500 mt-1">Hazijaanza</p>
                    </div>
                    <div class="absolute bottom-0 right-0 opacity-5">
                        <svg class="h-32 w-32 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Mitihani Iliyopangiwa - Cards Style -->
            <div>
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-black text-gray-900">Mitihani Yako</h2>
                    <Link :href="route('emas.marker.history')" class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-green-700 transition-all">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Historia
                    </Link>
                </div>

                <div v-if="assignedExams.length === 0" class="text-center py-16 bg-white rounded-2xl shadow-lg">
                    <div class="mx-auto h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hakuna Mitihani</h3>
                    <p class="text-gray-600">Bado hujapewa mitihani ya kuweka alama</p>
                </div>

                <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="exam in assignedExams" :key="exam.id" class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-xl hover:shadow-2xl transition-all border-l-4 border-green-500">
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold text-white shadow-md" :class="getStatusBadge(exam.status).color">
                                {{ getStatusBadge(exam.status).text }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <h3 class="text-lg font-black text-gray-900 mb-1 pr-20">{{ exam.exam_name }}</h3>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ exam.exam_code }}</p>
                        </div>

                        <div class="space-y-3 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                                    <svg class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-green-700">{{ exam.subject }}</p>
                                    <p class="text-xs text-gray-600 font-semibold">{{ exam.class_form }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100">
                                    <svg class="h-5 w-5 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ formatDate(exam.exam_date) }}</p>
                                    <p class="text-xs text-gray-600 font-semibold">{{ exam.start_time }} - {{ exam.end_time }}</p>
                                </div>
                            </div>
                        </div>

                        <Link :href="route('emas.marker.exams.marks', exam.id)" class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 px-4 py-3 text-sm font-black text-white shadow-lg hover:from-green-700 hover:to-emerald-700 transition-all">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            WEKA ALAMA
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </MarkerLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    center: Object,
    candidates: Object,
    subjects: Array,
    stats: Object,
});

const formatNumber = (num) => new Intl.NumberFormat('en-US').format(num);

const getGradeColor = (grade) => {
    if (!grade) return '';
    const g = grade.toUpperCase();
    if (g === 'A') return 'text-green-700 bg-green-100';
    if (g === 'B') return 'text-blue-700 bg-blue-100';
    if (g === 'C') return 'text-yellow-700 bg-yellow-100';
    if (g === 'D') return 'text-orange-700 bg-orange-100';
    if (g === 'F') return 'text-red-700 bg-red-100';
    return 'text-gray-700';
};
</script>

<template>
    <Head :title="`Results - ${center.center_name}`" />

    <EmasLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <Link :href="route('emas.centers.show', center.id)" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-black text-gray-900">Matokeo ya Mitihani</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ center.center_name }} ({{ center.center_code }})</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-purple-500">
                    <p class="text-sm font-medium text-gray-600">Wanafunzi</p>
                    <p class="mt-2 text-3xl font-bold text-purple-700">{{ formatNumber(stats.total_candidates) }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-600">Mitihani</p>
                    <p class="mt-2 text-3xl font-bold text-blue-700">{{ formatNumber(stats.total_exams) }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-emerald-500">
                    <p class="text-sm font-medium text-gray-600">Wastani</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700">{{ stats.average_score ? stats.average_score.toFixed(1) : '0.0' }}%</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mb-4 flex items-center justify-between rounded-lg bg-white p-4 shadow-sm">
            <div>
                <p class="text-sm font-bold text-gray-900">{{ candidates.total }} wanafunzi jumla</p>
            </div>
            <div class="flex gap-3">
                <button class="rounded-lg bg-emerald-600 hover:bg-emerald-700 px-6 py-2.5 text-white font-black transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Excel
                </button>
                <button class="rounded-lg bg-blue-600 hover:bg-blue-700 px-6 py-2.5 text-white font-black transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <!-- Results Table -->
        <div class="rounded-xl bg-white shadow-sm overflow-hidden border-2 border-purple-300">
            <div v-if="candidates.data && candidates.data.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300 border-collapse">
                    <!-- Header -->
                    <thead class="sticky top-0 z-10">
                        <tr>
                            <th colspan="4" class="bg-purple-700 px-4 py-4 text-center text-base font-black text-white uppercase border border-gray-300">
                                {{ center.center_name }}<br>
                                <span class="text-sm">EMAS EXAMINATION RESULTS SHEET</span>
                            </th>
                            <th v-for="subject in subjects" :key="subject" :colspan="2" class="bg-indigo-600 px-3 py-3 text-center text-xs font-black text-white uppercase border border-gray-300">
                                {{ subject }}
                            </th>
                            <th rowspan="2" class="bg-purple-700 px-4 py-4 text-center text-xs font-black text-white uppercase border border-gray-300">
                                TOTAL<br>
                                <span class="text-xs font-normal">MARKS</span>
                            </th>
                        </tr>
                        <tr>
                            <th class="bg-gray-100 px-3 py-3 text-center text-xs font-black text-gray-700 uppercase border border-gray-300 sticky left-0 z-10">S/N</th>
                            <th class="bg-gray-100 px-4 py-3 text-left text-xs font-black text-gray-700 uppercase border border-gray-300 min-w-[140px]">REG NUMBER</th>
                            <th class="bg-gray-100 px-6 py-3 text-left text-xs font-black text-gray-700 uppercase border border-gray-300 min-w-[300px]">CANDIDATE FULL NAME</th>
                            <th class="bg-gray-100 px-3 py-3 text-center text-xs font-black text-gray-700 uppercase border border-gray-300">SEX</th>
                            <template v-for="subject in subjects" :key="subject">
                                <th class="bg-gray-50 px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase border border-gray-300 w-20">SCORE</th>
                                <th class="bg-gray-50 px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase border border-gray-300 w-20">GRADE</th>
                            </template>
                        </tr>
                    </thead>

                    <!-- Body -->
                    <tbody class="bg-white">
                        <tr v-for="(candidate, index) in candidates.data" :key="candidate.id" class="hover:bg-purple-50 transition-colors">
                            <td class="px-3 py-4 text-center text-sm font-bold text-gray-900 border border-gray-300 sticky left-0 bg-white z-10">
                                {{ candidates.from + index }}
                            </td>
                            <td class="px-4 py-4 text-left text-sm font-black text-purple-700 border border-gray-300 bg-purple-50">
                                {{ candidate.registration_number }}
                            </td>
                            <td class="px-6 py-4 text-left text-sm font-bold text-gray-900 border border-gray-300">
                                {{ candidate.full_name }}
                            </td>
                            <td class="px-3 py-4 text-center border border-gray-300">
                                <span :class="candidate.gender === 'M' ? 'text-blue-700' : 'text-pink-700'" class="text-sm font-black">
                                    {{ candidate.gender }}
                                </span>
                            </td>
                            <template v-for="subject in subjects" :key="subject">
                                <td class="px-3 py-4 text-center text-sm font-bold border border-gray-300">
                                    {{ candidate.results?.[subject]?.score || '-' }}
                                </td>
                                <td class="px-3 py-4 text-center text-sm font-black border border-gray-300" :class="getGradeColor(candidate.results?.[subject]?.grade)">
                                    {{ candidate.results?.[subject]?.grade || '-' }}
                                </td>
                            </template>
                            <td class="px-4 py-4 text-center text-base font-black border border-gray-300 bg-purple-50">
                                {{ candidate.total_score || '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-else class="p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hakuna Matokeo</h3>
                <p class="text-gray-600">Ongeza wanafunzi na matokeo yao</p>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="candidates.data && candidates.data.length > 0" class="mt-6 flex items-center justify-between rounded-lg bg-white px-6 py-4 shadow-sm">
            <div class="text-sm text-gray-700">
                Kuonyesha <span class="font-bold">{{ candidates.from }}</span> hadi <span class="font-bold">{{ candidates.to }}</span> kati ya <span class="font-bold">{{ candidates.total }}</span> wanafunzi
            </div>
            <div class="flex gap-2">
                <Link 
                    v-if="candidates.prev_page_url" 
                    :href="candidates.prev_page_url" 
                    class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50"
                    preserve-scroll
                >
                    Nyuma
                </Link>
                <span v-else class="rounded-lg border-2 border-gray-200 px-4 py-2 text-sm font-bold text-gray-400 cursor-not-allowed">
                    Nyuma
                </span>
                <Link 
                    v-if="candidates.next_page_url" 
                    :href="candidates.next_page_url" 
                    class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50"
                    preserve-scroll
                >
                    Mbele
                </Link>
                <span v-else class="rounded-lg border-2 border-gray-200 px-4 py-2 text-sm font-bold text-gray-400 cursor-not-allowed">
                    Mbele
                </span>
            </div>
        </div>
    </EmasLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MarkerLayout from '@/Layouts/MarkerLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    history: {
        type: Object,
        required: true
    }
});

const searchQuery = ref('');

const filteredHistory = computed(() => {
    if (!searchQuery.value) return props.history.data;
    
    const query = searchQuery.value.toLowerCase();
    return props.history.data.filter(item => 
        item.candidate?.full_name?.toLowerCase().includes(query) ||
        item.candidate?.registration_number?.toLowerCase().includes(query) ||
        item.exam?.exam_name?.toLowerCase().includes(query)
    );
});

const formatDate = (date) => {
    if (!date) return '--';
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getGradeColor = (grade) => {
    const colors = {
        'A': 'bg-green-100 text-green-800',
        'B': 'bg-blue-100 text-blue-800',
        'C': 'bg-yellow-100 text-yellow-800',
        'D': 'bg-amber-100 text-amber-800',
        'E': 'bg-orange-100 text-orange-800',
        'F': 'bg-red-100 text-red-800'
    };
    return colors[grade] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Marking History - EMAS" />

    <MarkerLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div>
                    <Link :href="route('emas.marker.dashboard')" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 mb-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Dashboard
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900">Marking History</h1>
                    <p class="mt-1 text-sm text-gray-600">View all marks you have entered</p>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="rounded-lg bg-gray-50 p-4">
                <div class="relative max-w-md">
                    <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="searchQuery" type="text" placeholder="Search by candidate name, reg. number, or exam..." class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                </div>
            </div>

            <!-- History Table -->
            <div class="rounded-lg bg-white shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Date & Time
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Exam
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Candidate
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Reg. Number
                                </th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Score
                                </th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Grade
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Remarks
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="item in filteredHistory" :key="item.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">
                                    {{ formatDate(item.updated_at) }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ item.exam?.exam_name || '--' }}</div>
                                    <div class="text-xs text-gray-500">{{ item.exam?.subject || '--' }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ item.candidate?.full_name || '--' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-700">
                                    {{ item.candidate?.registration_number || '--' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-center text-sm font-bold text-gray-900">
                                    {{ item.score }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-center">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" :class="getGradeColor(item.grade)">
                                        {{ item.grade }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ item.remarks || '--' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="filteredHistory.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No history found</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ searchQuery ? 'No marks match your search.' : 'You haven\'t entered any marks yet.' }}</p>
                </div>

                <!-- Pagination -->
                <div v-if="history.links && history.links.length > 3" class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="history.prev_page_url" :href="history.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="history.next_page_url" :href="history.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ history.from }}</span> to <span class="font-medium">{{ history.to }}</span> of <span class="font-medium">{{ history.total }}</span> results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <Link v-for="(link, index) in history.links" :key="index" :href="link.url" v-html="link.label" :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        index === 0 ? 'rounded-l-md' : '',
                                        index === history.links.length - 1 ? 'rounded-r-md' : ''
                                    ]" />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MarkerLayout>
</template>

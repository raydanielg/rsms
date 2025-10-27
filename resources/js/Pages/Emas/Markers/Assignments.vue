<script setup>
import { Head, Link } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

defineProps({
    assignments: Object,
});
</script>

<template>
    <Head title="Marker Assignments - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Marker Assignments</h1>
                    <p class="mt-1 text-sm text-gray-600">Track exam marking assignments and progress</p>
                </div>
            </div>

            <!-- Assignments Table -->
            <div class="rounded-lg bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Marker</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Exam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Papers</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Dates</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="assignment in assignments.data" :key="assignment.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ assignment.marker?.name }}</p>
                                        <p class="text-sm text-gray-500">{{ assignment.marker?.marker_code }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ assignment.exam?.exam_name }}</p>
                                        <p class="text-sm text-gray-500">{{ assignment.exam?.subject }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <p class="text-gray-900">Assigned: <span class="font-semibold">{{ assignment.papers_assigned }}</span></p>
                                        <p class="text-emerald-600">Marked: <span class="font-semibold">{{ assignment.papers_marked }}</span></p>
                                        <p class="text-amber-600">Pending: <span class="font-semibold">{{ assignment.papers_pending }}</span></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="w-full">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ Math.round((assignment.papers_marked / assignment.papers_assigned) * 100) }}%
                                            </span>
                                        </div>
                                        <div class="h-2 w-full rounded-full bg-gray-200">
                                            <div class="h-2 rounded-full bg-emerald-500 transition-all" 
                                                 :style="{ width: `${(assignment.papers_marked / assignment.papers_assigned) * 100}%` }">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="assignment.status === 'assigned'" class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                                        Assigned
                                    </span>
                                    <span v-else-if="assignment.status === 'in_progress'" class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-800">
                                        In Progress
                                    </span>
                                    <span v-else class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">
                                        <p v-if="assignment.assigned_at">Assigned: {{ new Date(assignment.assigned_at).toLocaleDateString() }}</p>
                                        <p v-if="assignment.completed_at" class="text-emerald-600">Completed: {{ new Date(assignment.completed_at).toLocaleDateString() }}</p>
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

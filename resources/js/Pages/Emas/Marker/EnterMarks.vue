<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import MarkerLayout from '@/Layouts/MarkerLayout.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    exam: {
        type: Object,
        required: true
    },
    candidates: {
        type: Array,
        default: () => []
    }
});

// Form state
const results = ref([]);
const searchQuery = ref('');
const filterStatus = ref('all'); // all, unmarked, marked

// Initialize results array from candidates
results.value = props.candidates.map(candidate => ({
    candidate_id: candidate.id,
    candidate_name: candidate.full_name,
    registration_number: candidate.registration_number,
    score: candidate.results?.[0]?.score || null,
    grade: candidate.results?.[0]?.grade || '',
    remarks: candidate.results?.[0]?.remarks || '',
    comments: candidate.results?.[0]?.comments || '',
    is_marked: !!candidate.results?.[0]
}));

// Auto-calculate grade based on score
const calculateGrade = (score, totalMarks) => {
    if (!score || score === '' || score === null) return '';
    const percentage = (score / totalMarks) * 100;
    if (percentage >= 75) return 'A';
    if (percentage >= 65) return 'B';
    if (percentage >= 50) return 'C';
    if (percentage >= 40) return 'D';
    if (percentage >= 30) return 'E';
    return 'F';
};

// Auto-calculate remarks
const calculateRemarks = (score, totalMarks) => {
    if (!score || score === '' || score === null) return '';
    const percentage = (score / totalMarks) * 100;
    if (percentage >= 75) return 'Excellent';
    if (percentage >= 65) return 'Very Good';
    if (percentage >= 50) return 'Good';
    if (percentage >= 40) return 'Satisfactory';
    return 'Fail';
};

// Update grade and remarks when score changes
const handleScoreInput = (index) => {
    const result = results.value[index];
    if (result.score !== null && result.score !== '') {
        result.grade = calculateGrade(result.score, props.exam.total_marks);
        result.remarks = calculateRemarks(result.score, props.exam.total_marks);
        result.is_marked = true;
    } else {
        result.grade = '';
        result.remarks = '';
        result.is_marked = false;
    }
};

// Filtered candidates
const filteredResults = computed(() => {
    let filtered = results.value;
    
    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(r => 
            r.candidate_name.toLowerCase().includes(query) ||
            r.registration_number.toLowerCase().includes(query)
        );
    }
    
    // Status filter
    if (filterStatus.value === 'marked') {
        filtered = filtered.filter(r => r.is_marked);
    } else if (filterStatus.value === 'unmarked') {
        filtered = filtered.filter(r => !r.is_marked);
    }
    
    return filtered;
});

// Statistics
const stats = computed(() => ({
    total: results.value.length,
    marked: results.value.filter(r => r.is_marked).length,
    unmarked: results.value.filter(r => !r.is_marked).length,
    percentage: results.value.length ? Math.round((results.value.filter(r => r.is_marked).length / results.value.length) * 100) : 0
}));

// Form submission
const form = useForm({
    results: []
});

const saving = ref(false);

const saveMarks = () => {
    // Prepare data for submission - only include candidates with scores
    const markedResults = results.value
        .filter(r => r.score !== null && r.score !== '' && r.score >= 0)
        .map(r => ({
            candidate_id: r.candidate_id,
            score: parseInt(r.score),
            grade: r.grade,
            remarks: r.remarks,
            comments: r.comments || null
        }));
    
    if (markedResults.length === 0) {
        alert('Please enter marks for at least one candidate before saving.');
        return;
    }
    
    form.results = markedResults;
    saving.value = true;
    
    form.post(route('emas.marker.exams.marks.store', props.exam.id), {
        preserveScroll: true,
        onSuccess: () => {
            saving.value = false;
            // Update is_marked status
            results.value.forEach(r => {
                if (r.score !== null && r.score !== '') {
                    r.is_marked = true;
                }
            });
        },
        onError: () => {
            saving.value = false;
        }
    });
};

// Quick actions
const markAllAsZero = () => {
    if (confirm('Are you sure you want to mark all unmarked candidates as 0? This action cannot be undone.')) {
        results.value.forEach((r, index) => {
            if (!r.is_marked) {
                r.score = 0;
                handleScoreInput(index);
            }
        });
    }
};

const clearAll = () => {
    if (confirm('Are you sure you want to clear all marks? This action cannot be undone.')) {
        results.value.forEach(r => {
            r.score = null;
            r.grade = '';
            r.remarks = '';
            r.comments = '';
            r.is_marked = false;
        });
    }
};
</script>

<template>
    <Head :title="`Enter Marks - ${exam.exam_name}`" />

    <MarkerLayout>
        <div class="space-y-6">
            <!-- Exam Header -->
            <div class="flex items-start justify-between">
                <div>
                    <Link :href="route('emas.marker.dashboard')" class="inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 mb-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Dashboard
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900">{{ exam.exam_name }}</h1>
                    <div class="mt-1 flex items-center gap-4 text-sm text-gray-600">
                        <span class="inline-flex items-center gap-1">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            {{ exam.subject }}
                        </span>
                        <span>{{ exam.class_form }}</span>
                        <span>Total Marks: {{ exam.total_marks }}</span>
                        <span>Pass Marks: {{ exam.pass_marks }}</span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 sm:grid-cols-4">
                <div class="rounded-lg bg-gradient-to-br from-green-50 to-emerald-100 p-4 border-2 border-green-200">
                    <p class="text-sm font-medium text-green-700">Jumla ya Wanafunzi</p>
                    <p class="mt-1 text-2xl font-bold text-green-900">{{ stats.total }}</p>
                </div>
                <div class="rounded-lg bg-gradient-to-br from-yellow-50 to-amber-100 p-4 border-2 border-yellow-300">
                    <p class="text-sm font-medium text-yellow-800">Wamewekwa Alama</p>
                    <p class="mt-1 text-2xl font-bold text-green-700">{{ stats.marked }}</p>
                </div>
                <div class="rounded-lg bg-gradient-to-br from-orange-50 to-red-50 p-4 border-2 border-orange-200">
                    <p class="text-sm font-medium text-orange-700">Hawajakamilika</p>
                    <p class="mt-1 text-2xl font-bold text-orange-900">{{ stats.unmarked }}</p>
                </div>
                <div class="rounded-lg bg-gradient-to-br from-emerald-50 to-teal-100 p-4 border-2 border-emerald-300">
                    <p class="text-sm font-medium text-emerald-700">Maendeleo</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-900">{{ stats.percentage }}%</p>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between rounded-lg bg-gray-50 p-4">
                <div class="flex flex-1 items-center gap-3">
                    <div class="relative flex-1 max-w-md">
                        <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input v-model="searchQuery" type="text" placeholder="Tafuta kwa jina au namba ya usajili..." class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200">
                    </div>
                    <select v-model="filterStatus" class="rounded-lg border border-gray-300 py-2 px-4 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200">
                        <option value="all">All Candidates</option>
                        <option value="marked">Marked Only</option>
                        <option value="unmarked">Unmarked Only</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="clearAll" type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Clear All
                    </button>
                    <button @click="saveMarks" :disabled="saving" type="button" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:opacity-50 shadow-md">
                        <svg v-if="!saving" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg v-else class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ saving ? 'Saving...' : 'Save Marks' }}
                    </button>
                </div>
            </div>

            <!-- Marks Entry Table -->
            <div class="rounded-lg bg-white shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 w-12">
                                    #
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Reg. Number
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Candidate Name
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 w-32">
                                    Score
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 w-20">
                                    Grade
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 w-32">
                                    Remarks
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Comments
                                </th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 w-24">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="(result, index) in filteredResults" :key="result.candidate_id" :class="{ 'bg-green-50': result.is_marked }">
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ index + 1 }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ result.registration_number }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ result.candidate_name }}
                                </td>
                                <td class="px-4 py-3">
                                    <input v-model.number="result.score" @input="handleScoreInput(results.indexOf(result))" type="number" min="0" :max="exam.total_marks" class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200" placeholder="0">
                                </td>
                                <td class="px-4 py-3">
                                    <input v-model="result.grade" type="text" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-sm text-center font-bold" :class="{ 'text-green-700': ['A', 'B'].includes(result.grade), 'text-amber-700': ['C', 'D'].includes(result.grade), 'text-red-700': ['E', 'F'].includes(result.grade) }">
                                </td>
                                <td class="px-4 py-3">
                                    <input v-model="result.remarks" type="text" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-xs">
                                </td>
                                <td class="px-4 py-3">
                                    <input v-model="result.comments" type="text" class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200" placeholder="Maoni (si lazima)">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span v-if="result.is_marked" class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Marked
                                    </span>
                                    <span v-else class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                        Pending
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="filteredResults.length === 0" class="text-center py-12">
                    <p class="text-sm text-gray-500">No candidates found matching your filters.</p>
                </div>
            </div>
        </div>
    </MarkerLayout>
</template>

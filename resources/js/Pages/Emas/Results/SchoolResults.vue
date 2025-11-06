<script setup>
import { Head, Link } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    center: Object,
    candidates: Object,
    subjects: Array,
    stats: Object,
    exams: Array,
});

// Debug: Log props to console
console.log('SchoolResults Props:', props);

const formatNumber = (num) => new Intl.NumberFormat('sw-TZ').format(num);

const getGradeColor = (grade) => {
    if (!grade) return 'bg-gray-100 text-gray-600';
    const g = grade.toUpperCase();
    if (g === 'A') return 'bg-emerald-100 text-emerald-800 font-black';
    if (g === 'B') return 'bg-blue-100 text-blue-800 font-bold';
    if (g === 'C') return 'bg-yellow-100 text-yellow-800 font-bold';
    if (g === 'D') return 'bg-orange-100 text-orange-800 font-bold';
    if (g === 'F') return 'bg-red-100 text-red-800 font-bold';
    return 'bg-gray-100 text-gray-600';
};

const getDivisionColor = (division) => {
    if (!division) return 'text-gray-600';
    const div = String(division).toUpperCase();
    if (div === 'I') return 'text-emerald-700 font-black';
    if (div === 'II') return 'text-blue-700 font-bold';
    if (div === 'III') return 'text-yellow-700 font-bold';
    if (div === 'IV') return 'text-orange-700 font-bold';
    if (div === '0') return 'text-red-700 font-bold';
    return 'text-gray-600';
};

// Calculate division summary by gender
const divisionSummary = computed(() => {
    const summary = {
        F: { I: 0, II: 0, III: 0, IV: 0, '0': 0 },
        M: { I: 0, II: 0, III: 0, IV: 0, '0': 0 },
        T: { I: 0, II: 0, III: 0, IV: 0, '0': 0 }
    };
    
    if (props.candidates?.data) {
        props.candidates.data.forEach(candidate => {
            const div = candidate.division || '0';
            const gender = candidate.gender === 'F' ? 'F' : 'M';
            
            if (summary[gender].hasOwnProperty(div)) {
                summary[gender][div]++;
                summary.T[div]++;
            }
        });
    }
    
    return summary;
});

// Calculate subject statistics
const subjectStats = computed(() => {
    const stats = {};
    
    if (!props.candidates?.data || !props.subjects) return [];
    
    props.subjects.forEach(subject => {
        stats[subject] = {
            name: subject,
            total: 0,
            A: 0,
            B: 0,
            C: 0,
            D: 0,
            F: 0,
            sum: 0,
            count: 0,
        };
    });
    
    props.candidates.data.forEach(candidate => {
        if (candidate.results) {
            Object.keys(candidate.results).forEach(subject => {
                if (stats[subject]) {
                    stats[subject].total++;
                    const grade = candidate.results[subject].grade;
                    const score = candidate.results[subject].score;
                    
                    if (grade) stats[subject][grade]++;
                    if (score) {
                        stats[subject].sum += score;
                        stats[subject].count++;
                    }
                }
            });
        }
    });
    
    return Object.values(stats).map(stat => ({
        ...stat,
        average: stat.count > 0 ? (stat.sum / stat.count).toFixed(1) : '0.0',
        passRate: stat.total > 0 ? (((stat.A + stat.B + stat.C + stat.D) / stat.total) * 100).toFixed(1) : '0.0',
    }));
});

const handlePrint = () => {
    window.print();
};

// Get Excel-style subject header colors
const getExcelSubjectColor = (index) => {
    const colors = [
        '#b3d9ff', // Light blue
        '#c2f0c2', // Light green
        '#ffe0b3', // Light orange
        '#ffd9d9', // Light red
        '#e6ccff', // Light purple
        '#fff9b3', // Light yellow
        '#d9f0ff', // Sky blue
        '#ffccf2', // Pink
        '#ccffe6', // Mint
        '#ffd9b3', // Peach
        '#d9d9ff', // Lavender
        '#ffffcc', // Cream
        '#ccf2ff', // Ice blue
        '#ffb3d9', // Rose
        '#b3ffe6', // Aqua
    ];
    return colors[index % colors.length];
};

// Get Excel-style cell background based on score
const getExcelCellColor = (subjectIndex, score) => {
    if (!score || score === '-') return '#ffffff';
    
    // Base colors matching subject headers (lighter)
    const baseColors = [
        '#e6f3ff', // Lighter blue
        '#e6f9e6', // Lighter green
        '#fff5e6', // Lighter orange
        '#fff0f0', // Lighter red
        '#f5ebff', // Lighter purple
        '#fffef0', // Lighter yellow
        '#f0f9ff', // Lighter sky
        '#ffe6f5', // Lighter pink
        '#e6fff5', // Lighter mint
        '#fff0e6', // Lighter peach
        '#f0f0ff', // Lighter lavender
        '#fffff5', // Lighter cream
        '#e6f9ff', // Lighter ice
        '#ffe6f0', // Lighter rose
        '#e6fff9', // Lighter aqua
    ];
    
    return baseColors[subjectIndex % baseColors.length];
};

// Get division color in hex format
const getDivisionColorHex = (division) => {
    if (!division) return '#6b7280';
    const div = String(division).toUpperCase();
    if (div === 'I') return '#047857'; // emerald-700
    if (div === 'II') return '#1e40af'; // blue-700
    if (div === 'III') return '#ca8a04'; // yellow-700
    if (div === 'IV') return '#c2410c'; // orange-700
    if (div === '0') return '#dc2626'; // red-700
    return '#6b7280';
};

// Get subject header colors (rotating vibrant colors) - Keep for other uses
const getSubjectHeaderColor = (index) => {
    const colors = [
        'bg-gradient-to-br from-green-400 to-green-500 text-white shadow-md',
        'bg-gradient-to-br from-blue-400 to-blue-500 text-white shadow-md',
        'bg-gradient-to-br from-purple-400 to-purple-500 text-white shadow-md',
        'bg-gradient-to-br from-pink-400 to-pink-500 text-white shadow-md',
        'bg-gradient-to-br from-yellow-400 to-yellow-500 text-gray-900 shadow-md',
        'bg-gradient-to-br from-indigo-400 to-indigo-500 text-white shadow-md',
        'bg-gradient-to-br from-red-400 to-red-500 text-white shadow-md',
        'bg-gradient-to-br from-teal-400 to-teal-500 text-white shadow-md',
        'bg-gradient-to-br from-orange-400 to-orange-500 text-white shadow-md',
        'bg-gradient-to-br from-cyan-400 to-cyan-500 text-white shadow-md',
        'bg-gradient-to-br from-lime-400 to-lime-500 text-gray-900 shadow-md',
        'bg-gradient-to-br from-rose-400 to-rose-500 text-white shadow-md',
        'bg-gradient-to-br from-emerald-400 to-emerald-500 text-white shadow-md',
        'bg-gradient-to-br from-violet-400 to-violet-500 text-white shadow-md',
        'bg-gradient-to-br from-fuchsia-400 to-fuchsia-500 text-white shadow-md',
    ];
    return colors[index % colors.length];
};

// Get subject cell colors based on grade
const getSubjectCellColor = (subjectIndex, grade) => {
    if (!grade) return 'bg-gray-50 text-gray-500';
    
    const g = grade.toUpperCase();
    
    // Base colors for subjects (lighter versions matching headers)
    const baseColors = [
        { bg: 'bg-green-50', border: 'border-l-4 border-green-400' },
        { bg: 'bg-blue-50', border: 'border-l-4 border-blue-400' },
        { bg: 'bg-purple-50', border: 'border-l-4 border-purple-400' },
        { bg: 'bg-pink-50', border: 'border-l-4 border-pink-400' },
        { bg: 'bg-yellow-50', border: 'border-l-4 border-yellow-400' },
        { bg: 'bg-indigo-50', border: 'border-l-4 border-indigo-400' },
        { bg: 'bg-red-50', border: 'border-l-4 border-red-400' },
        { bg: 'bg-teal-50', border: 'border-l-4 border-teal-400' },
        { bg: 'bg-orange-50', border: 'border-l-4 border-orange-400' },
        { bg: 'bg-cyan-50', border: 'border-l-4 border-cyan-400' },
        { bg: 'bg-lime-50', border: 'border-l-4 border-lime-400' },
        { bg: 'bg-rose-50', border: 'border-l-4 border-rose-400' },
        { bg: 'bg-emerald-50', border: 'border-l-4 border-emerald-400' },
        { bg: 'bg-violet-50', border: 'border-l-4 border-violet-400' },
        { bg: 'bg-fuchsia-50', border: 'border-l-4 border-fuchsia-400' },
    ];
    
    const colorSet = baseColors[subjectIndex % baseColors.length];
    
    // Grade-based text color and weight
    if (g === 'A') return `${colorSet.bg} ${colorSet.border} text-emerald-700 font-black`;
    if (g === 'B') return `${colorSet.bg} ${colorSet.border} text-blue-700 font-bold`;
    if (g === 'C') return `${colorSet.bg} ${colorSet.border} text-yellow-700 font-semibold`;
    if (g === 'D') return `${colorSet.bg} ${colorSet.border} text-orange-700`;
    if (g === 'F') return `${colorSet.bg} ${colorSet.border} text-red-700 font-semibold`;
    
    return `${colorSet.bg} ${colorSet.border} text-gray-700`;
};
</script>

<template>
    <Head :title="`Matokeo - ${center.center_name}`" />

    <EmasLayout>
        <div class="space-y-6 print:space-y-4">
            <!-- Header with Back Button and Print -->
            <div class="flex items-center justify-between gap-4 print:hidden">
                <div class="flex items-center gap-4">
                    <Link 
                        :href="route('emas.results.index')" 
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-white border-2 border-gray-300 text-gray-600 hover:bg-gray-50 hover:border-emerald-500 hover:text-emerald-600 transition-all"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900">{{ center.center_name }}</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="font-bold text-emerald-600">{{ center.center_code }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ center.district }}, {{ center.region }}</span>
                        </p>
                    </div>
                </div>
                
                <!-- Print Button -->
                <button 
                    @click="handlePrint"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-8 py-3 text-white font-bold transition-colors shadow-lg hover:shadow-xl"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Results
                </button>
            </div>

            <!-- Official Header -->
            <div class="text-center mb-2 py-2 bg-white border-b-2 border-gray-400 print:py-3 print:border-b">
                <div class="text-[8px] uppercase tracking-wide text-gray-500 print:text-[7px]">The President's Office - Regional Administration and Local Government</div>
                <div class="text-lg font-black text-emerald-800 uppercase mt-1 print:text-sm">
                    {{ center.center_name }}
                </div>
                <div class="text-xs font-bold text-gray-600 uppercase print:text-[9px]">
                    EMAS Results {{ new Date().getFullYear() }}
                </div>
                <div class="text-[10px] text-gray-500 print:text-[8px]">
                    {{ center.center_code }} | {{ center.district }}, {{ center.region }}
                </div>
            </div>

            <!-- Division Performance Summary -->
            <div class="bg-white mb-2 pb-2 border-b-2 border-gray-300 print:mb-1 print:pb-1 print:border-b">
                <h3 class="text-[10px] font-bold text-gray-700 mb-1 text-center uppercase print:text-[8px]">Division Summary</h3>
                <div class="flex justify-center">
                    <table class="border-collapse border border-gray-400 print:text-[7px]">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-400 px-3 py-0.5 text-[10px] font-bold text-gray-800 print:px-2 print:text-[7px]">SEX</th>
                                <th class="border border-gray-400 px-3 py-0.5 text-[10px] font-bold text-emerald-700 print:px-2 print:text-[7px]">I</th>
                                <th class="border border-gray-400 px-3 py-0.5 text-[10px] font-bold text-blue-700 print:px-2 print:text-[7px]">II</th>
                                <th class="border border-gray-400 px-3 py-0.5 text-[10px] font-bold text-yellow-700 print:px-2 print:text-[7px]">III</th>
                                <th class="border border-gray-400 px-3 py-0.5 text-[10px] font-bold text-orange-700 print:px-2 print:text-[7px]">IV</th>
                                <th class="border border-gray-400 px-3 py-0.5 text-[10px] font-bold text-red-700 print:px-2 print:text-[7px]">0</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-pink-50">
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-[10px] font-bold text-pink-700 print:px-2 print:text-[7px]">F</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.F.I }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.F.II }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.F.III }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.F.IV }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.F['0'] }}</td>
                            </tr>
                            <tr class="bg-blue-50">
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-[10px] font-bold text-blue-700 print:px-2 print:text-[7px]">M</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.M.I }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.M.II }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.M.III }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.M.IV }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-xs font-bold text-gray-800 print:px-2 print:text-[7px]">{{ divisionSummary.M['0'] }}</td>
                            </tr>
                            <tr class="bg-emerald-50">
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-[10px] font-bold text-emerald-700 print:px-2 print:text-[7px]">T</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-sm font-black text-emerald-700 print:px-2 print:text-[8px]">{{ divisionSummary.T.I }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-sm font-black text-blue-700 print:px-2 print:text-[8px]">{{ divisionSummary.T.II }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-sm font-black text-yellow-700 print:px-2 print:text-[8px]">{{ divisionSummary.T.III }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-sm font-black text-orange-700 print:px-2 print:text-[8px]">{{ divisionSummary.T.IV }}</td>
                                <td class="border border-gray-400 px-3 py-0.5 text-center text-sm font-black text-red-700 print:px-2 print:text-[8px]">{{ divisionSummary.T['0'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Results Table (Excel Style) -->
            <div class="bg-white overflow-hidden print:shadow-none">
                <div v-if="candidates?.data && candidates.data.length > 0" ref="tableContainer" class="overflow-x-auto">
                    <table class="w-full border-collapse min-w-max" style="font-size: 10px;">
                        <!-- Header -->
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-400 px-2 py-2 text-center text-[9px] font-black text-gray-800 uppercase sticky left-0 bg-gray-100 z-20" style="min-width: 40px;">
                                    CNO
                                </th>
                                <th class="border border-gray-400 px-2 py-2 text-left text-[9px] font-black text-gray-800 uppercase sticky left-[40px] bg-gray-100 z-20" style="min-width: 180px;">
                                    CANDIDATE FULL NAME
                                </th>
                                <th class="border border-gray-400 px-2 py-2 text-center text-[9px] font-black text-gray-800 uppercase bg-gray-100" style="min-width: 35px;">
                                    SEX
                                </th>
                                <th v-for="(subject, idx) in subjects" :key="`subj-${idx}`" class="border border-gray-400 px-1 py-2 text-center text-[8px] font-black uppercase" style="min-width: 45px;" :style="{ background: getExcelSubjectColor(idx) }">
                                    <div class="transform" style="writing-mode: vertical-rl; text-orientation: mixed; transform: rotate(180deg); height: 70px; display: flex; align-items: center; justify-content: center;">
                                        {{ subject }}
                                    </div>
                                </th>
                                <th class="border border-gray-400 px-2 py-2 text-center text-[9px] font-black text-gray-800 uppercase bg-emerald-100" style="min-width: 50px;">
                                    TOTAL
                                </th>
                                <th class="border border-gray-400 px-2 py-2 text-center text-[9px] font-black text-gray-800 uppercase bg-blue-100" style="min-width: 45px;">
                                    AVG
                                </th>
                                <th class="border border-gray-400 px-2 py-2 text-center text-[9px] font-black text-gray-800 uppercase bg-purple-100" style="min-width: 40px;">
                                    DIV
                                </th>
                            </tr>
                        </thead>

                        <!-- Body -->
                        <tbody class="bg-white">
                            <tr v-for="(candidate, index) in candidates.data" :key="candidate.id" class="hover:bg-yellow-50 transition-colors">
                                <td class="border border-gray-300 px-2 py-1.5 text-center text-[10px] font-bold text-gray-900 sticky left-0 bg-white z-10">
                                    {{ candidates.from + index }}
                                </td>
                                <td class="border border-gray-300 px-2 py-1.5 text-left text-[10px] font-bold text-gray-900 sticky left-[40px] bg-white z-10">
                                    {{ candidate.full_name }}
                                </td>
                                <td class="border border-gray-300 px-2 py-1.5 text-center text-[10px] font-black" :class="candidate.gender === 'M' ? 'text-blue-700' : 'text-pink-700'">
                                    {{ candidate.gender }}
                                </td>
                                <td v-for="(subject, idx) in subjects" :key="`cell-${idx}`" class="border border-gray-300 px-2 py-1.5 text-center text-sm font-black" :style="{ background: getExcelCellColor(idx, candidate.results?.[subject]?.score) }">
                                    {{ candidate.results?.[subject]?.score || '' }}
                                </td>
                                <td class="border border-gray-300 px-2 py-1.5 text-center text-xs font-black text-emerald-700 bg-emerald-50">
                                    {{ candidate.total_score || '0' }}
                                </td>
                                <td class="border border-gray-300 px-2 py-1.5 text-center text-xs font-black text-blue-700 bg-blue-50">
                                    {{ candidate.average_score || '0' }}
                                </td>
                                <td class="border border-gray-300 px-2 py-1.5 text-center text-sm font-black bg-purple-50" :class="getDivisionColor(candidate.division)">
                                    {{ candidate.division || '0' }}
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
                    <p class="text-gray-600">Bado hakuna matokeo yaliyopatikana kwa shule hii</p>
                </div>
            </div>

            <!-- Subject Performance Statistics -->
            <div v-if="subjectStats.length > 0" class="rounded-xl bg-white shadow-sm overflow-hidden border-2 border-gray-200 print:break-before-page print:border print:shadow-none">
                <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4">
                    <h3 class="text-lg font-black text-white uppercase">Takwimu za Masomo</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-800 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-800 uppercase">Somo</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-slate-800 uppercase">Wanafunzi</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-slate-800 uppercase">Wastani</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-slate-800 uppercase">% Waliofaulu</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-emerald-700 uppercase">A</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-blue-700 uppercase">B</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-yellow-700 uppercase">C</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-orange-700 uppercase">D</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-red-700 uppercase">F</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="(stat, index) in subjectStats" :key="stat.name" class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm font-bold text-slate-700">{{ index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ stat.name }}</td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-slate-700">{{ stat.total }}</td>
                                <td class="px-4 py-3 text-center text-sm font-black text-slate-900">{{ stat.average }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold" :class="parseFloat(stat.passRate) >= 80 ? 'bg-emerald-100 text-emerald-800' : parseFloat(stat.passRate) >= 60 ? 'bg-blue-100 text-blue-800' : parseFloat(stat.passRate) >= 40 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'">
                                        {{ stat.passRate }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-emerald-700">{{ stat.A }}</td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-blue-700">{{ stat.B }}</td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-yellow-700">{{ stat.C }}</td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-orange-700">{{ stat.D }}</td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-red-700">{{ stat.F }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="candidates?.data && candidates.data.length > 0" class="flex items-center justify-between rounded-lg bg-white px-6 py-4 shadow-sm border border-gray-200 print:hidden">
                <div class="text-sm text-gray-700">
                    Kuonyesha <span class="font-bold text-gray-900">{{ candidates?.from || 0 }}</span> hadi <span class="font-bold text-gray-900">{{ candidates?.to || 0 }}</span> kati ya <span class="font-bold text-gray-900">{{ candidates?.total || 0 }}</span> wanafunzi
                </div>
                <div class="flex gap-2">
                    <Link 
                        v-if="candidates?.prev_page_url" 
                        :href="candidates.prev_page_url"
                        class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors"
                        preserve-scroll
                    >
                        Nyuma
                    </Link>
                    <span v-else class="rounded-lg border-2 border-gray-200 px-4 py-2 text-sm font-bold text-gray-400 cursor-not-allowed">
                        Nyuma
                    </span>
                    <Link 
                        v-if="candidates?.next_page_url" 
                        :href="candidates.next_page_url"
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

            <!-- Copyright Footer (Print Only) -->
            <div class="hidden print:block mt-2 pt-2 border-t-2 border-gray-400">
                <div class="text-center space-y-1">
                    <div class="text-[7px] text-gray-600">
                        © {{ new Date().getFullYear() }} Results Management System (RSMS)
                    </div>
                    <div class="text-[7px] text-gray-500">
                        Printed on {{ new Date().toLocaleDateString('en-GB') }} at {{ new Date().toLocaleTimeString('en-GB') }}
                    </div>
                    <div class="text-[7px] text-gray-500">
                        This is an official document from {{ center.center_name }}
                    </div>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

<style scoped>
/* Sticky column support */
.sticky {
    position: sticky;
}

@media print {
    /* Page setup - A4 Landscape ONLY */
    @page {
        size: A4 landscape !important;
        margin: 0.5cm 0.5cm !important;
    }
    
    html, body {
        width: 297mm !important;
        height: 210mm !important;
    }
    
    /* Ensure colors print correctly */
    * {
        print-color-adjust: exact !important;
        -webkit-print-color-adjust: exact !important;
    }
    
    /* Hide all navigation, sidebar, header, footer, buttons */
    nav,
    aside,
    header:not(.print-header),
    footer:not(.print-footer),
    button,
    .sidebar,
    .menu,
    .navigation,
    [role="navigation"],
    [role="banner"],
    [aria-label="Sidebar"] {
        display: none !important;
    }
    
    /* Hide non-printable elements */
    .print\:hidden {
        display: none !important;
    }
    
    .print\:block {
        display: block !important;
    }
    
    /* Ensure main content takes full width */
    body,
    html,
    main,
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    /* Remove shadows and rounded corners for print */
    .print\:shadow-none {
        box-shadow: none !important;
    }
    
    .print\:border {
        border: 1px solid #999 !important;
    }
    
    /* Page break controls */
    .print\:break-before-page {
        page-break-before: always;
    }
    
    /* Table printing */
    table {
        page-break-inside: auto;
        font-size: 8px !important;
        width: 100%;
    }
    
    thead {
        display: table-header-group;
    }
    
    tbody {
        display: table-row-group;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    /* Remove sticky positioning in print */
    .sticky {
        position: static !important;
    }
    
    /* Optimize spacing for print */
    body {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Ensure full width usage */
    .space-y-6 {
        gap: 0 !important;
        margin: 0 !important;
    }
    
    /* Compact everything for print */
    h1, h2, h3 {
        margin: 0 !important;
        padding: 2px 0 !important;
    }
    
    /* Division summary - extra compact */
    .border-b-2 {
        margin-bottom: 2px !important;
        padding-bottom: 2px !important;
    }
    
    /* Results table cells compact */
    th, td {
        padding: 1px 2px !important;
        font-size: 6px !important;
        line-height: 1.1 !important;
    }
    
    /* Subject headers in table */
    th div {
        font-size: 6px !important;
        height: 50px !important;
    }
    
    /* Ensure backgrounds print */
    table thead th,
    table tbody td {
        background-color: inherit !important;
    }
    
    /* Make everything fit */
    * {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }
}


/* Responsive styles for smaller screens */
@media (max-width: 1536px) {
    table {
        font-size: 11px;
    }
    
    th, td {
        padding: 6px 8px;
    }
    
    th div {
        height: 90px;
        font-size: 10px;
    }
}

@media (max-width: 1280px) {
    table {
        font-size: 10px;
    }
    
    th, td {
        padding: 4px 6px;
    }
    
    th div {
        height: 80px;
        font-size: 9px;
    }
    
    th[style*="min-width: 250px"] {
        min-width: 200px !important;
    }
}

@media (max-width: 1024px) {
    table {
        font-size: 9px;
    }
    
    th, td {
        padding: 3px 5px;
    }
    
    th div {
        height: 70px;
        font-size: 8px;
    }
    
    th[style*="min-width: 250px"] {
        min-width: 180px !important;
    }
    
    th[style*="min-width: 60px"] {
        min-width: 45px !important;
    }
}

@media (max-width: 768px) {
    table {
        font-size: 8px;
    }
    
    th, td {
        padding: 2px 4px;
    }
    
    th div {
        height: 60px;
        font-size: 7px;
    }
    
    th[style*="min-width: 250px"] {
        min-width: 150px !important;
    }
    
    th[style*="min-width: 60px"] {
        min-width: 40px !important;
    }
}

/* Smooth scrolling */
.overflow-x-auto {
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f7fafc;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>

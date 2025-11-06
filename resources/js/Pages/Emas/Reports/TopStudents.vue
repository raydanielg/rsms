<script setup>
import { Head } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

defineProps({
    students: Array,
    filters: Object,
    title: String,
});

const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="Top Students Report - EMAS" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between print:hidden">
                <div>
                    <h1 class="text-2xl font-black text-gray-900">{{ title || 'Top Students Report' }}</h1>
                    <p class="mt-1 text-sm text-gray-600">Student performance rankings</p>
                </div>
                <button 
                    @click="printReport" 
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-white font-bold hover:bg-blue-700 transition-colors shadow-lg"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Report
                </button>
            </div>

            <!-- Results Section -->
            <div class="bg-white print:shadow-none">
                <!-- Official Header -->
                <div class="text-center py-4 border-b-4 border-gray-800 mb-6 print:border-b-2 print:mb-3">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-700 print:text-[8px]">THE PRESIDENT'S OFFICE</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-700 mt-1 print:text-[8px]">REGIONAL ADMINISTRATION AND LOCAL GOVERNMENT</div>
                    <div class="text-lg font-black uppercase text-gray-900 mt-3 print:text-sm">MWANZA REGION</div>
                    <div class="text-sm font-bold uppercase text-gray-800 mt-2 print:text-[10px]">REGIONAL FORM FOUR PRE-NATIONAL EXAMINATION RESULTS, OCTOBER {{ filters?.year || 2025 }}</div>
                    <div class="text-base font-black uppercase text-gray-900 mt-3 bg-amber-200 py-2 print:text-xs print:py-1">
                        {{ title || 'TOP TEN STUDENTS' }}
                    </div>
                </div>

                <!-- Filter Summary -->
                <div v-if="filters" class="mb-4 print:hidden bg-amber-50 border-l-4 border-amber-600 p-4 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-900 mb-2">Applied Filters:</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                                <div v-if="filters.region"><span class="font-semibold">Region:</span> {{ filters.region }}</div>
                                <div v-if="filters.district"><span class="font-semibold">District:</span> {{ filters.district }}</div>
                                <div v-if="filters.school_type"><span class="font-semibold">School Type:</span> {{ filters.school_type }}</div>
                                <div v-if="filters.gender"><span class="font-semibold">Gender:</span> {{ filters.gender }}</div>
                                <div v-if="filters.report_type"><span class="font-semibold">Report Type:</span> {{ filters.report_type }}</div>
                                <div v-if="filters.year"><span class="font-semibold">Year:</span> {{ filters.year }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border-2 border-gray-800" style="font-size: 10px;">
                        <thead>
                            <tr class="bg-amber-100">
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900 uppercase" style="background-color: #FFEB9C;">C/NO</th>
                                <th class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase" style="background-color: #FFEB9C;">SCHOOL NAME</th>
                                <th class="border-2 border-gray-800 px-4 py-2 text-center font-black text-gray-900 uppercase" style="background-color: #FFEB9C;">CANDIDATE FULL NAME</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900 uppercase" style="background-color: #FFEB9C;">SEX</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900 uppercase" style="background-color: #FFEB9C;">AVERAGE</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900 uppercase" style="background-color: #FFEB9C;">GRADE</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900 uppercase" style="background-color: #FFEB9C;">AGCT</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900 uppercase" style="background-color: #FFEB9C;">AVG</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900 uppercase" style="background-color: #FFEB9C;">POSITION</th>
                                <th colspan="20" class="border-2 border-gray-800 px-4 py-2 text-center font-black text-gray-900 uppercase">DETAILED SUBJECTS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!students || students.length === 0">
                                <td colspan="29" class="border border-gray-600 px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <div>
                                            <p class="text-lg font-bold text-gray-700">No Student Data Available</p>
                                            <p class="text-sm text-gray-500 mt-1">Please select filters to generate report</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(student, index) in students" :key="student.candidate_id" class="hover:bg-yellow-50 transition-colors">
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold">{{ student.candidate_number }}</td>
                                <td class="border border-gray-600 px-3 py-2 text-left font-bold uppercase text-[9px]">{{ student.school_name }}</td>
                                <td class="border border-gray-600 px-4 py-2 text-left font-bold uppercase">{{ student.full_name }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-black" :class="{
                                    'text-blue-700': student.gender === 'M',
                                    'text-pink-700': student.gender === 'F'
                                }">{{ student.gender }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-black text-emerald-700">{{ student.average }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-black" :class="{
                                    'text-emerald-700': student.grade === 'A',
                                    'text-blue-700': student.grade === 'B',
                                    'text-yellow-700': student.grade === 'C',
                                    'text-orange-700': student.grade === 'D'
                                }">{{ student.grade }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-black">{{ student.agct || 7 }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-black">{{ student.avg || 7 }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-black text-lg" :class="{
                                    'text-amber-700': index < 3,
                                    'text-gray-800': index >= 3
                                }">{{ index + 1 }}</td>
                                <!-- Subject Scores -->
                                <td v-for="(score, subject) in student.subjects" :key="subject" class="border border-gray-600 px-1 py-2 text-center text-[8px]">
                                    {{ score || '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="hidden print:block mt-4 pt-3 border-t-2 border-gray-400">
                    <div class="text-center space-y-1">
                        <div class="text-[7px] text-gray-600">
                            © {{ new Date().getFullYear() }} Results Management System (RSMS)
                        </div>
                        <div class="text-[7px] text-gray-500">
                            Generated on {{ new Date().toLocaleDateString('en-GB') }} at {{ new Date().toLocaleTimeString('en-GB') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmasLayout>
</template>

<style scoped>
@media print {
    @page {
        size: A4 landscape !important;
        margin: 0.5cm !important;
    }
    
    * {
        print-color-adjust: exact !important;
        -webkit-print-color-adjust: exact !important;
    }
    
    table {
        font-size: 6px !important;
    }
    
    th, td {
        padding: 1px 2px !important;
    }
}
</style>

<script setup>
import { Head } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

defineProps({
    rankings: Array,
    filters: Object,
});

const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="School Rankings - EMAS Reports" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-gray-900">School Rankings Report</h1>
                    <p class="mt-1 text-sm text-gray-600">School performance rankings by region and subject</p>
                </div>
                <button 
                    @click="printReport" 
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-white font-bold hover:bg-blue-700 transition-colors shadow-lg print:hidden"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Report
                </button>
            </div>

            <!-- Results Section -->
            <div class="bg-white print:shadow-none">
                <!-- Official Header (Print) -->
                <div class="text-center py-4 border-b-4 border-gray-800 mb-6 print:border-b-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-700">THE PRESIDENT'S OFFICE</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-700 mt-1">REGIONAL ADMINISTRATION AND LOCAL GOVERNMENT</div>
                    <div class="text-lg font-black uppercase text-gray-900 mt-3">MWANZA REGION</div>
                    <div class="text-sm font-bold uppercase text-gray-800 mt-2">REGIONAL FORM FOUR PRE-NATIONAL EXAMINATION RESULTS, OCTOBER 2025</div>
                    <div class="text-sm font-bold uppercase text-gray-800 mt-1">ILEMELA MC SCHOOLS RANK SUBJECTWISE</div>
                    <div class="text-base font-black uppercase text-gray-900 mt-2 bg-gray-200 py-2">
                        SCHOOL RANK IN {{ filters?.subject?.toUpperCase() || 'ALL SUBJECTS' }} COUNCILWISE
                    </div>
                </div>

                <!-- Filter Summary (Screen Only) -->
                <div v-if="filters" class="mb-4 print:hidden bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-900 mb-2">Applied Filters:</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                                <div v-if="filters.region"><span class="font-semibold">Region:</span> {{ filters.region }}</div>
                                <div v-if="filters.district"><span class="font-semibold">District:</span> {{ filters.district }}</div>
                                <div v-if="filters.ward"><span class="font-semibold">Ward:</span> {{ filters.ward }}</div>
                                <div v-if="filters.subject"><span class="font-semibold">Subject:</span> {{ filters.subject }}</div>
                                <div v-if="filters.year"><span class="font-semibold">Year:</span> {{ filters.year }}</div>
                                <div v-if="filters.exam_type"><span class="font-semibold">Exam Type:</span> {{ filters.exam_type }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rankings Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border-2 border-gray-800" style="font-size: 10px;">
                        <thead>
                            <tr class="bg-gray-100">
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">S/N</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-4 py-2 text-center font-black text-gray-900 uppercase">SCHOOL NAME</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">OWNERSHIP</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">AV</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">GRD</th>
                                <th colspan="6" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase bg-blue-100">GRADING PERFORMANCE</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">A-C</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">%A-C</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">A-D</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">%A-D</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">GPA</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-4 py-2 text-center font-black text-gray-900 uppercase">COMPETENCY LEVEL</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black text-gray-900 uppercase">CNW</th>
                            </tr>
                            <tr class="bg-gray-100">
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900">A</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900">B</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900">C</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900">D</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900">F</th>
                                <th class="border-2 border-gray-800 px-2 py-2 text-center font-black text-gray-900">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Real Data from Database -->
                            <tr v-if="!rankings || rankings.length === 0">
                                <td colspan="18" class="border border-gray-600 px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <p class="text-lg font-bold text-gray-700">No Data Available</p>
                                            <p class="text-sm text-gray-500 mt-1">Please select filters from Subject Statistics page to generate report</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(school, index) in rankings" :key="school.center_id" class="hover:bg-yellow-50 transition-colors">
                                <td class="border border-gray-600 px-3 py-2 text-center font-bold">{{ String(index + 1).padStart(3, '0') }}</td>
                                <td class="border border-gray-600 px-4 py-2 text-left font-bold uppercase">{{ school.center_name }}</td>
                                <td class="border border-gray-600 px-3 py-2 text-center text-xs uppercase">{{ school.ownership }}</td>
                                <td class="border border-gray-600 px-3 py-2 text-center font-bold">{{ school.average }}</td>
                                <td class="border border-gray-600 px-3 py-2 text-center font-black" :class="{
                                    'text-emerald-700': school.grade === 'A',
                                    'text-blue-700': school.grade === 'B',
                                    'text-yellow-700': school.grade === 'C',
                                    'text-orange-700': school.grade === 'D',
                                    'text-red-700': school.grade === 'F'
                                }">{{ school.grade }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ school.grade_a }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ school.grade_b }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ school.grade_c }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ school.grade_d }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ school.grade_f }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold">{{ school.total_sat }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold">{{ school.ac_count }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold" :class="{
                                    'text-emerald-700': school.ac_percentage >= 80,
                                    'text-blue-700': school.ac_percentage >= 60 && school.ac_percentage < 80,
                                    'text-yellow-700': school.ac_percentage >= 40 && school.ac_percentage < 60,
                                    'text-red-700': school.ac_percentage < 40
                                }">{{ school.ac_percentage }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold">{{ school.ad_count }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold" :class="{
                                    'text-emerald-700': school.ad_percentage >= 90,
                                    'text-blue-700': school.ad_percentage >= 70 && school.ad_percentage < 90,
                                    'text-yellow-700': school.ad_percentage >= 50 && school.ad_percentage < 70,
                                    'text-red-700': school.ad_percentage < 50
                                }">{{ school.ad_percentage }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold">{{ school.gpa }}</td>
                                <td class="border border-gray-600 px-4 py-2 text-left text-xs font-bold" :class="{
                                    'bg-emerald-100 text-emerald-800': school.grade === 'A' || school.grade === 'B',
                                    'bg-yellow-100 text-yellow-800': school.grade === 'C',
                                    'bg-orange-100 text-orange-800': school.grade === 'D',
                                    'bg-red-100 text-red-800': school.grade === 'F'
                                }">{{ school.competency_level }}</td>
                                <td class="border border-gray-600 px-3 py-2 text-center font-black text-lg">{{ school.rank }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer (Print Only) -->
                <div class="hidden print:block mt-6 pt-4 border-t-2 border-gray-400">
                    <div class="text-center space-y-1">
                        <div class="text-xs text-gray-600">
                            © {{ new Date().getFullYear() }} Results Management System (RSMS)
                        </div>
                        <div class="text-xs text-gray-500">
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
        font-size: 7px !important;
    }
    
    th, td {
        padding: 2px 4px !important;
    }
}
</style>

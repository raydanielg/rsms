<script setup>
import { Head } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

defineProps({
    rankings: Array,
    summary: Object,
    filters: Object,
});

const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="Ward Rankings - EMAS Reports" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-gray-900">Ward Rankings Report</h1>
                    <p class="mt-1 text-sm text-gray-600">Ward performance rankings and statistics</p>
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
                <!-- Official Header -->
                <div class="text-center py-4 border-b-4 border-gray-800 mb-6 print:border-b-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-700">REGIONAL ADMINISTRATION AND LOCAL GOVERNMENT</div>
                    <div class="text-lg font-black uppercase text-gray-900 mt-2">MWANZA REGION</div>
                    <div class="text-sm font-bold uppercase text-gray-800 mt-2">REGIONAL FORM FOUR PRE-NATIONAL EXAMINATION RESULTS, OCTOBER 2025</div>
                    <div class="text-base font-black uppercase text-gray-900 mt-3 bg-blue-200 py-2">
                        ILEMELA MC WARDS PERFORMANCE
                    </div>
                </div>

                <!-- Summary Row (% PASS) -->
                <div class="mb-4">
                    <table class="w-full border-collapse border-2 border-gray-800" style="font-size: 9px;">
                        <thead>
                            <tr class="bg-gray-200">
                                <th rowspan="2" class="border-2 border-gray-800 px-2 py-2 text-center font-black">SUMMARY<br/>PERFORMANCE</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-2 py-2 text-center font-black">NO. OF WARDS IN<br/>COUNCIL</th>
                                <th colspan="4" class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-yellow-100">NUMBER OF CANDIDATES</th>
                                <th colspan="8" class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-green-100">DIVISION PERFORMANCE</th>
                                <th colspan="3" class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-blue-100">AVERAGE</th>
                                <th colspan="2" class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-purple-100">GPA PERFORMANCE</th>
                            </tr>
                            <tr class="bg-gray-200">
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">REG</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">TOTAL</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">%</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">SAT</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">I</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">II</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">III</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs bg-green-200">I-III</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">IV</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs bg-green-200">I-IV</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs bg-red-200">DIV - 0</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs bg-red-200">%</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">AV</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">GRADE</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">GPA</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">COMPETENCY LEVEL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="summary" class="bg-green-100 font-bold">
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">% PASS</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.total_wards }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.total_registered }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.total_sat }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.sat_percentage }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.grade_i }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.grade_ii }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.grade_iii }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.i_iii }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold bg-green-200">{{ summary.i_iii_percentage }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.grade_iv }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.i_iv }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold bg-green-200">{{ summary.i_iv_percentage }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ summary.grade_0 }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold bg-red-200">{{ summary.div_0_percentage }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ Number(summary.average).toFixed(2) }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.grade }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-bold">{{ Number(summary.gpa).toFixed(5) }}</td>
                                <td class="border-2 border-gray-800 px-3 py-2 text-left font-bold bg-emerald-100 text-emerald-800">{{ summary.competency_level }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Wards Performance Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border-2 border-gray-800" style="font-size: 9px;">
                        <thead>
                            <tr class="bg-gray-200">
                                <th rowspan="2" class="border-2 border-gray-800 px-2 py-2 text-center font-black">S/NO</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-4 py-2 text-center font-black">WARD</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-2 py-2 text-center font-black">NUMBER OF<br/>SCHOOLS</th>
                                <th colspan="4" class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-yellow-100">NUMBER OF CANDIDATES</th>
                                <th colspan="8" class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-green-100">DIVISION PERFORMANCE</th>
                                <th colspan="2" class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-blue-100">AVERAGE</th>
                                <th colspan="2" class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-purple-100">GPA PERFORMANCE</th>
                                <th rowspan="2" class="border-2 border-gray-800 px-2 py-2 text-center font-black">RANK</th>
                            </tr>
                            <tr class="bg-gray-200">
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">REG</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">TOTAL</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">%</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">SAT</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">I</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">II</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">III</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs bg-green-200">I-III</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">IV</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs bg-green-200">I-IV</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs bg-red-200">DIV-0</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs bg-red-200">%</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">AV</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">GRADE</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">GPA</th>
                                <th class="border-2 border-gray-800 px-2 py-1 text-center font-black text-xs">COMPETENCY LEVEL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Real Ward Data -->
                            <tr v-if="!rankings || rankings.length === 0">
                                <td colspan="20" class="border border-gray-600 px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                        </svg>
                                        <div>
                                            <p class="text-lg font-bold text-gray-700">No Ward Data Available</p>
                                            <p class="text-sm text-gray-500 mt-1">Please select filters to generate ward performance report</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(ward, index) in rankings" :key="ward.ward_name" class="hover:bg-yellow-50 transition-colors">
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold">{{ String(index + 1).padStart(2, '0') }}</td>
                                <td class="border border-gray-600 px-3 py-2 text-left font-bold uppercase">{{ ward.ward_name }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ ward.schools_count }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ ward.total_registered }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ ward.total_sat }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold">{{ ward.sat_percentage }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ ward.grade_i }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ ward.grade_ii }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ ward.grade_iii }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center bg-green-100">{{ ward.i_iii }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold" :class="{
                                    'text-emerald-700': ward.i_iii_percentage >= 80,
                                    'text-blue-700': ward.i_iii_percentage >= 60 && ward.i_iii_percentage < 80,
                                    'text-yellow-700': ward.i_iii_percentage < 60
                                }">{{ ward.i_iii_percentage }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ ward.grade_iv }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center bg-green-100 font-bold">{{ ward.i_iv }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold" :class="{
                                    'text-emerald-700': ward.i_iv_percentage >= 90,
                                    'text-blue-700': ward.i_iv_percentage >= 70 && ward.i_iv_percentage < 90,
                                    'text-yellow-700': ward.i_iv_percentage < 70
                                }">{{ ward.i_iv_percentage }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center bg-red-100">{{ ward.grade_0 }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center bg-red-100">{{ ward.div_0_percentage }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-bold">{{ ward.average }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-black" :class="{
                                    'text-emerald-700': ward.grade === 'A',
                                    'text-blue-700': ward.grade === 'B',
                                    'text-yellow-700': ward.grade === 'C',
                                    'text-orange-700': ward.grade === 'D',
                                    'text-red-700': ward.grade === 'F'
                                }">{{ ward.grade }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center">{{ Number(ward.gpa).toFixed(5) }}</td>
                                <td class="border border-gray-600 px-3 py-2 text-left text-xs font-bold" :class="{
                                    'bg-emerald-100 text-emerald-800': ward.grade === 'A' || ward.grade === 'B',
                                    'bg-yellow-100 text-yellow-800': ward.grade === 'C',
                                    'bg-orange-100 text-orange-800': ward.grade === 'D',
                                    'bg-red-100 text-red-800': ward.grade === 'F'
                                }">{{ ward.competency_level }}</td>
                                <td class="border border-gray-600 px-2 py-2 text-center font-black text-lg">{{ ward.rank }}</td>
                            </tr>

                            <!-- TOTAL Row -->
                            <tr v-if="summary" class="bg-gray-300 font-black">
                                <td colspan="2" class="border-2 border-gray-800 px-3 py-2 text-center font-black">TOTAL</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.total_schools }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.total_registered }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.total_sat }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.sat_percentage }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.grade_i }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.grade_ii }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.grade_iii }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-green-200">{{ summary.i_iii }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.i_iii_percentage }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.grade_iv }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-green-200">{{ summary.i_iv }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.i_iv_percentage }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-red-200">{{ summary.grade_0 }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black bg-red-200">{{ summary.div_0_percentage }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ Number(summary.average).toFixed(2) }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ summary.grade }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center font-black">{{ Number(summary.gpa).toFixed(5) }}</td>
                                <td class="border-2 border-gray-800 px-3 py-2 text-left font-black bg-emerald-100 text-emerald-800">{{ summary.competency_level }}</td>
                                <td class="border-2 border-gray-800 px-2 py-2 text-center"></td>
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
        padding: 1px 2px !important;
    }
}
</style>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MarkerLayout from '@/Layouts/MarkerLayout.vue';
import { ref } from 'vue';

const openSection = ref(null);

const toggleSection = (section) => {
    openSection.value = openSection.value === section ? null : section;
};
</script>

<template>
    <Head title="Help Guide - EMAS Marker Panel" />

    <MarkerLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-2xl bg-gradient-to-br from-green-600 via-emerald-600 to-teal-600 p-8 text-white shadow-2xl">
                <div class="flex items-center gap-4">
                    <div class="rounded-2xl bg-yellow-400 p-4">
                        <svg class="h-12 w-12 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-yellow-300">Mwongozo wa Matumizi</h1>
                        <p class="text-lg text-white mt-1">Jifunze jinsi ya kutumia EMAS Marker Panel</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid gap-4 sm:grid-cols-3">
                <a href="#kuanza" class="group rounded-2xl bg-white p-6 shadow-lg hover:shadow-xl transition-all border-l-4 border-green-500">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-green-100 p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Kuanza Haraka</p>
                            <p class="text-xs text-gray-600">Hatua za msingi</p>
                        </div>
                    </div>
                </a>

                <a href="#marks" class="group rounded-2xl bg-white p-6 shadow-lg hover:shadow-xl transition-all border-l-4 border-yellow-500">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-yellow-100 p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Kuweka Alama</p>
                            <p class="text-xs text-gray-600">Jinsi ya kuingiza</p>
                        </div>
                    </div>
                </a>

                <a href="#faqs" class="group rounded-2xl bg-white p-6 shadow-lg hover:shadow-xl transition-all border-l-4 border-blue-500">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Maswali Yanayoulizwa</p>
                            <p class="text-xs text-gray-600">FAQs</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Getting Started -->
            <div id="kuanza" class="rounded-2xl bg-white p-8 shadow-lg">
                <h2 class="text-2xl font-black text-gray-900 mb-6 flex items-center gap-3">
                    <span class="rounded-lg bg-green-100 p-2">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                    Kuanza Haraka
                </h2>

                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white font-black text-xl flex-shrink-0">1</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Ingia kwenye Mfumo</h3>
                            <p class="text-gray-600 leading-relaxed">Tumia username na password yako kuingia kwenye EMAS Marker Panel. Utapelekwa automatically kwenye dashboard yako.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white font-black text-xl flex-shrink-0">2</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Angalia Mitihani Yako</h3>
                            <p class="text-gray-600 leading-relaxed">Kwenye dashboard, utaona mitihani yote uliyopangiwa kuweka alama. Kila mtihani una maelezo ya somo, darasa, na tarehe.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white font-black text-xl flex-shrink-0">3</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Chagua Mtihani</h3>
                            <p class="text-gray-600 leading-relaxed">Bonyeza kitufe cha "WEKA ALAMA" kwenye mtihani unaouhitaji. Utafunguliwa ukurasa wa kuingiza alama.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white font-black text-xl flex-shrink-0">4</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Weka Alama</h3>
                            <p class="text-gray-600 leading-relaxed">Ingiza alama kwa kila mwanafunzi. Mfumo utakokotoa daraja na maoni automatic. Bonyeza "Save Marks" kukamilisha.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marks Entry Guide -->
            <div id="marks" class="rounded-2xl bg-white p-8 shadow-lg">
                <h2 class="text-2xl font-black text-gray-900 mb-6 flex items-center gap-3">
                    <span class="rounded-lg bg-yellow-100 p-2">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </span>
                    Mwongozo wa Kuweka Alama
                </h2>

                <div class="space-y-6">
                    <div class="rounded-xl bg-green-50 p-6 border-l-4 border-green-500">
                        <h3 class="text-lg font-bold text-green-900 mb-3">Vipimo vya Daraja</h3>
                        <div class="grid sm:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-green-700">A:</span>
                                <span class="text-gray-700">75% - 100% (Excellent)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-green-700">B:</span>
                                <span class="text-gray-700">65% - 74% (Very Good)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-yellow-700">C:</span>
                                <span class="text-gray-700">50% - 64% (Good)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-yellow-700">D:</span>
                                <span class="text-gray-700">40% - 49% (Satisfactory)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-orange-700">E:</span>
                                <span class="text-gray-700">30% - 39% (Fail)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-red-700">F:</span>
                                <span class="text-gray-700">0% - 29% (Fail)</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-blue-50 p-6 border-l-4 border-blue-500">
                        <h3 class="text-lg font-bold text-blue-900 mb-3">Maelekezo Muhimu</h3>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Hakikisha alama ni sahihi kabla ya kuhifadhi</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Daraja na maoni yanakokotolewa automatic</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Unaweza kuweka maoni ya ziada kwa kila mwanafunzi</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Tumia kipengele cha "Search" kutafuta mwanafunzi</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- FAQs -->
            <div id="faqs" class="rounded-2xl bg-white p-8 shadow-lg">
                <h2 class="text-2xl font-black text-gray-900 mb-6 flex items-center gap-3">
                    <span class="rounded-lg bg-blue-100 p-2">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    Maswali Yanayoulizwa Sana
                </h2>

                <div class="space-y-4">
                    <div class="rounded-xl border-2 border-gray-200 overflow-hidden">
                        <button @click="toggleSection('q1')" class="w-full flex items-center justify-between p-5 hover:bg-gray-50 transition-colors">
                            <span class="font-bold text-gray-900 text-left">Je, naweza kubadilisha alama baada ya kuhifadhi?</span>
                            <svg class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openSection === 'q1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-show="openSection === 'q1'" class="p-5 bg-gray-50 border-t-2 border-gray-200">
                            <p class="text-gray-700">Ndio, unaweza kurudi kwenye mtihani na kubadilisha alama. Bonyeza "WEKA ALAMA" tena na ubadilishe alama zinazohitajika, kisha hifadhi.</p>
                        </div>
                    </div>

                    <div class="rounded-xl border-2 border-gray-200 overflow-hidden">
                        <button @click="toggleSection('q2')" class="w-full flex items-center justify-between p-5 hover:bg-gray-50 transition-colors">
                            <span class="font-bold text-gray-900 text-left">Je, nifanye nini kama mwanafunzi hakuhudhuria mtihani?</span>
                            <svg class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openSection === 'q2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-show="openSection === 'q2'" class="p-5 bg-gray-50 border-t-2 border-gray-200">
                            <p class="text-gray-700">Usiweke alama kwa mwanafunzi huyo. Acha space yake tupu au weka '0' na uweke maoni "ABSENT" kwenye comments.</p>
                        </div>
                    </div>

                    <div class="rounded-xl border-2 border-gray-200 overflow-hidden">
                        <button @click="toggleSection('q3')" class="w-full flex items-center justify-between p-5 hover:bg-gray-50 transition-colors">
                            <span class="font-bold text-gray-900 text-left">Ninaweza kuweka alama za mitihani mingapi kwa siku?</span>
                            <svg class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openSection === 'q3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-show="openSection === 'q3'" class="p-5 bg-gray-50 border-t-2 border-gray-200">
                            <p class="text-gray-700">Hakuna kikomo! Unaweza kuweka alama za mitihani yote iliyopangiwa kwako kwa siku moja au kuendelea kwa siku nyingi kulingana na uwezo wako.</p>
                        </div>
                    </div>

                    <div class="rounded-xl border-2 border-gray-200 overflow-hidden">
                        <button @click="toggleSection('q4')" class="w-full flex items-center justify-between p-5 hover:bg-gray-50 transition-colors">
                            <span class="font-bold text-gray-900 text-left">Je, naweza kuweka alama za somo ambalo sijapangiwa?</span>
                            <svg class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openSection === 'q4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-show="openSection === 'q4'" class="p-5 bg-gray-50 border-t-2 border-gray-200">
                            <p class="text-gray-700">Hapana. Mfumo utakuzuia automatic kuweka alama za masomo ambayo hujapangiwa. Wasiliana na coordinator kama unahitaji kubadilisha masomo yako.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support CTA -->
            <div class="rounded-2xl bg-gradient-to-r from-orange-500 to-red-600 p-8 text-white shadow-2xl">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="text-2xl font-black mb-2">Bado una Swali?</h3>
                        <p class="text-orange-100">Wasiliana na timu yetu ya usaidizi kwa msaada zaidi</p>
                    </div>
                    <Link :href="route('emas.marker.support')" class="rounded-xl bg-white px-8 py-4 font-black text-orange-600 hover:bg-gray-100 transition-all shadow-lg whitespace-nowrap">
                        Wasiliana na Support
                    </Link>
                </div>
            </div>
        </div>
    </MarkerLayout>
</template>

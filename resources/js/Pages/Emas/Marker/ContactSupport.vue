<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import MarkerLayout from '@/Layouts/MarkerLayout.vue';

const form = useForm({
    subject: '',
    category: '',
    message: '',
    priority: 'normal'
});

const submit = () => {
    form.post(route('emas.marker.support.send'), {
        onSuccess: () => {
            form.reset();
        }
    });
};
</script>

<template>
    <Head title="Contact Support - EMAS Marker Panel" />

    <MarkerLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-2xl bg-gradient-to-br from-orange-600 via-red-600 to-pink-600 p-8 text-white shadow-2xl">
                <div class="flex items-center gap-4">
                    <div class="rounded-2xl bg-yellow-400 p-4">
                        <svg class="h-12 w-12 text-orange-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-yellow-300">Wasiliana Na Support</h1>
                        <p class="text-lg text-white mt-1">Tutakusaidia haraka iwezekanavyo</p>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="rounded-2xl bg-white p-8 shadow-lg">
                        <h2 class="text-2xl font-black text-gray-900 mb-6">Tuma Ujumbe</h2>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Aina ya Tatizo *
                                </label>
                                <select v-model="form.category" required class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 text-gray-900 focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all">
                                    <option value="">Chagua aina...</option>
                                    <option value="login">Tatizo la Kuingia</option>
                                    <option value="marks">Tatizo la Kuweka Alama</option>
                                    <option value="technical">Tatizo la Kiufundi</option>
                                    <option value="account">Tatizo la Akaunti</option>
                                    <option value="other">Mengineyo</option>
                                </select>
                                <div v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</div>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Kipaumbele
                                </label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="relative">
                                        <input v-model="form.priority" type="radio" value="low" class="peer sr-only">
                                        <div class="rounded-xl border-2 border-gray-300 p-4 text-center cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 font-bold transition-all">
                                            Chini
                                        </div>
                                    </label>
                                    <label class="relative">
                                        <input v-model="form.priority" type="radio" value="normal" class="peer sr-only">
                                        <div class="rounded-xl border-2 border-gray-300 p-4 text-center cursor-pointer peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:text-yellow-700 font-bold transition-all">
                                            Wastani
                                        </div>
                                    </label>
                                    <label class="relative">
                                        <input v-model="form.priority" type="radio" value="high" class="peer sr-only">
                                        <div class="rounded-xl border-2 border-gray-300 p-4 text-center cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 font-bold transition-all">
                                            Juu
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Kichwa *
                                </label>
                                <input v-model="form.subject" type="text" required placeholder="Mfano: Siwezi kuona mitihani yangu" class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all">
                                <div v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</div>
                            </div>

                            <!-- Message -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Ujumbe Wako *
                                </label>
                                <textarea v-model="form.message" required rows="6" placeholder="Eleza tatizo lako kwa undani..." class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all resize-none"></textarea>
                                <div v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</div>
                                <p class="mt-2 text-xs text-gray-500">Eleza tatizo lako ili tuweze kukusaidia vizuri zaidi</p>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center gap-4">
                                <button type="submit" :disabled="form.processing" class="flex-1 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-4 font-black text-white shadow-lg hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 transition-all">
                                    <span v-if="!form.processing" class="flex items-center justify-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        Tuma Ujumbe
                                    </span>
                                    <span v-else class="flex items-center justify-center gap-2">
                                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Inatuma...
                                    </span>
                                </button>
                                <Link :href="route('emas.marker.dashboard')" class="rounded-xl border-2 border-gray-300 px-8 py-4 font-bold text-gray-700 hover:bg-gray-50 transition-all">
                                    Ghairi
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contact Info Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Contact -->
                    <div class="rounded-2xl bg-white p-6 shadow-lg">
                        <h3 class="text-lg font-black text-gray-900 mb-4">Njia Nyingine za Mawasiliano</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-green-100 p-2">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">Simu</p>
                                    <p class="text-gray-600 text-sm">+255 123 456 789</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-blue-100 p-2">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">Email</p>
                                    <p class="text-gray-600 text-sm">support@emas.go.tz</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-yellow-100 p-2">
                                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">Muda wa Kazi</p>
                                    <p class="text-gray-600 text-sm">Jumatatu - Ijumaa</p>
                                    <p class="text-gray-600 text-sm">8:00 AM - 5:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Help Guide Link -->
                    <div class="rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 p-6 text-white shadow-lg">
                        <h3 class="text-lg font-black mb-2">Tazama Mwongozo</h3>
                        <p class="text-sm text-green-100 mb-4">Labda maswali yako yamejibiwa tayari kwenye mwongozo</p>
                        <Link :href="route('emas.marker.help')" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-bold text-green-600 hover:bg-gray-100 transition-all">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Fungua Mwongozo
                        </Link>
                    </div>

                    <!-- Response Time -->
                    <div class="rounded-2xl bg-blue-50 p-6 border-2 border-blue-200">
                        <div class="flex items-center gap-3 mb-2">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="font-black text-blue-900">Muda wa Majibu</h3>
                        </div>
                        <p class="text-sm text-blue-800">Tunajitahidi kujibu ndani ya masaa 24. Tatizo zenye kipaumbele cha juu zinajibiwa haraka zaidi.</p>
                    </div>
                </div>
            </div>
        </div>
    </MarkerLayout>
</template>

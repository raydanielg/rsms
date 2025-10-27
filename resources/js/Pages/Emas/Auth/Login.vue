<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('emas.login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="EMAS Login" />

    <div class="min-h-screen flex items-center justify-center relative" style="background-image: url('/bg.jpg'); background-size: cover; background-position: center;">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/40 to-black/20"></div>
        
        <div class="w-full max-w-md px-4 relative z-10">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-yellow-500 rounded-full mb-4 shadow-2xl border-4 border-white">
                    <svg class="w-14 h-14 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold text-white mb-2" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">EMAS</h1>
                <p class="text-white/90 text-sm font-medium" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Exam Management & Administration System</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
                <div class="px-10 py-8">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Username</label>
                            <input
                                id="email"
                                type="text"
                                v-model="form.email"
                                required
                                autofocus
                                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-md text-gray-700 focus:border-green-500 focus:outline-none transition-colors"
                                placeholder="Enter your username"
                            />
                            <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Password</label>
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-md text-gray-700 focus:border-green-500 focus:outline-none transition-colors"
                                placeholder="Enter your password"
                            />
                            <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-600">{{ form.errors.password }}</p>
                        </div>

                        <div class="pt-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-md shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ form.processing ? 'SIGNING IN...' : 'SIGN IN' }}
                            </button>
                        </div>

                        <div class="text-center pt-2">
                            <Link href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                Forgot your password?
                            </Link>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="mt-6 text-center space-y-3">
                <div>
                    <Link :href="route('emas.register')" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-800 font-semibold rounded-md shadow-lg border-2 border-gray-300 hover:border-gray-400 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Create New Account
                    </Link>
                </div>
                <div>
                    <Link :href="route('login')" class="text-sm text-white hover:text-yellow-300 font-semibold transition-colors" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                        ← Back to RSMS Portal
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

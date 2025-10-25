<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Alert from '@/Components/Alert.vue';

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
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <Alert v-if="status" variant="success" title="Success" />

        <form @submit.prevent="submit" class="sm:rounded-xl">
            <h1 class="mb-6 text-center text-2xl font-semibold text-gray-800">Welcome back</h1>
            <div>
                <InputLabel for="email" value="Email or Username" />

                <TextInput
                    id="email"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter email or username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <div class="relative">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 mt-1 flex items-center px-2 text-gray-500 hover:text-gray-700"
                        aria-label="Toggle password visibility"
                    >
                        <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.07.207.07.437 0 .644C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678Z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.036 12.32c-.07.207-.07.437 0 .644C3.423 16.49 7.36 19 12 19c1.63 0 3.187-.275 4.622-.78M6.228 6.228A10.45 10.45 0 0 1 12 5c4.64 0 8.577 2.51 9.964 6.678.07.207.07.437 0 .644a10.523 10.523 0 0 1-1.523 2.474M3 3l18 18"/>
                        </svg>
                    </button>
                </div>

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-6 space-y-3">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                        </svg>
                        Processing...
                    </span>
                    <span v-else>
                        Log in
                    </span>
                </PrimaryButton>

                <div class="mt-2 flex items-center justify-between text-sm">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-gray-600 underline hover:text-gray-900"
                    >
                        Forgot your password?
                    </Link>
                    <Link
                        :href="route('register')"
                        class="ml-auto text-gray-600 underline hover:text-gray-900"
                    >
                        Don't have an account? Register
                    </Link>
                </div>
            </div>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white/90 px-2 text-gray-500">Or continue with</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <Link href="#" class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 bg-white/90 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303C34.361,31.912,29.638,35,24,35c-7.18,0-13-5.82-13-13 s5.82-13,13-13c3.314,0,6.313,1.231,8.594,3.235l5.657-5.657C34.046,3.053,29.268,1,24,1C11.85,1,2,10.85,2,23s9.85,22,22,22 s22-9.85,22-22C46,22.659,45.862,21.35,45.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,16.108,18.961,13,24,13c3.314,0,6.313,1.231,8.594,3.235l5.657-5.657 C34.046,3.053,29.268,1,24,1C15.317,1,7.004,6.243,6.306,14.691z"/><path fill="#4CAF50" d="M24,45c5.557,0,10.586-2.137,14.409-5.61l-6.652-5.623C29.623,35.74,26.956,37,24,37c-5.59,0-10.287-3.792-11.972-8.958 l-6.647,5.118C8.01,40.632,15.477,45,24,45z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-1.084,3.152-3.325,5.651-6.153,7.001c0.002-0.001,0.003-0.001,0.005-0.002 l6.652,5.623C35.591,42.863,46,36,46,23C46,22.659,45.862,21.35,45.611,20.083z"/></svg>
                        Google
                    </Link>
                    <Link href="#" class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 bg-white/90 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M213.3 174.1c-4.3 9.9-9.5 18.5-15.6 25.7-8.3 9.9-18.1 14.9-29.4 14.9-7 0-15.3-2.1-24.9-6.3-9.5-4.2-17.3-6.3-23.3-6.3-6.5 0-14.6 2.1-24.3 6.3-9.7 4.2-17.6 6.3-23.7 6.3-10.9 0-20.8-5.1-29.6-15.3-6-7-11.4-15.8-16.1-26.2-5.1-11.2-7.7-22-7.7-32.5 0-12.8 3.5-23.6 10.5-32.6 7-9 16.1-13.5 27.2-13.5 7.1 0 16.2 2.3 27.2 6.9 11 4.6 18.1 6.9 21.3 6.9 2.3 0 9-2.4 20.1-7.1 10.8-4.5 19.9-6.7 27.2-6.7 8.1 0 15.3 2.1 21.6 6.4 3.3 2.2 6.5 5 9.5 8.4-7.8 6.3-13.1 11.7-16 16.3-4.6 7.5-6.9 15.9-6.9 25 0 10.3 2.8 19.4 8.5 27.4 5.7 8 12.4 13.4 20.1 16.3zM164.1 41.6c0 6.8-2 13.4-6 19.8-7.6 12.7-17.1 19.1-28.3 18.2-.1-.9-.2-1.9-.2-3 0-6.5 2.3-13.3 7-20.3 3.5-5.5 8.1-10 13.8-13.6 5.7-3.6 11.1-5.6 16.1-6.1.4 1.7.6 3.3.6 5z"/></svg>
                        Apple
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

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
                    Log in
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
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path d="M16.365 1.43c.263.318.481.692.653 1.123.227.577.343 1.188.347 1.833 0 .71-.132 1.404-.397 2.082a5.38 5.38 0 0 1-1.18 1.86 4.92 4.92 0 0 1-1.607 1.11 5.25 5.25 0 0 1-1.993.397c-.283 0-.62-.08-1.01-.239-.39-.16-.77-.24-1.141-.24-.405 0-.8.081-1.186.244-.385.163-.73.25-1.034.26-.4.017-.808-.1-1.223-.352-.415-.252-.78-.588-1.093-1.01a5.64 5.64 0 0 1-.857-1.66 5.7 5.7 0 0 1-.333-1.91c0-.728.15-1.424.455-2.088a4.76 4.76 0 0 1 1.246-1.655C6.83 1.29 7.54 1 8.33 1c.45 0 .98.09 1.59.268.61.179 1.007.269 1.192.269.146 0 .53-.095 1.151-.284C12.885 1.065 13.466.97 14.01.97c.82 0 1.53.307 2.155.46l.2.001Z"/><path d="M16.53 12.628c.83 0 1.862.336 3.096 1.007 1.008.556 1.934 1.32 2.78 2.292-.72.55-1.46.983-2.22 1.298-.928.39-1.857.586-2.786.586-.72 0-1.562-.153-2.525-.46a5.9 5.9 0 0 0-1.65-.239c-.633 0-1.327.108-2.083.325-.756.218-1.462.327-2.118.327-.98 0-1.962-.207-2.947-.62-1.034-.433-2.002-1.05-2.904-1.85-.936-.833-1.697-1.787-2.283-2.86 1.006-.873 1.946-1.541 2.82-2.003 1.164-.607 2.312-.91 3.444-.91.73 0 1.568.145 2.517.434.948.289 1.677.435 2.187.435.46 0 1.119-.128 1.974-.385.854-.257 1.6-.385 2.236-.385Z"/></svg>
                        Apple
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const form = useForm({
    name: '',
    phone: '',
    region: '',
    school_name: '',
    school_number: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const regions = ref([
    'Dar es Salaam',
    'Dodoma',
    'Arusha',
    'Mwanza',
    'Kilimanjaro',
    'Mbeya',
    'Morogoro',
    'Tanga',
    'Mtwara',
    'Pwani',
    'Tabora',
]);

const availability = ref({
    email: null,
    phone: null,
    username: null,
    school_name: null,
});

const checking = ref({
    email: false,
    phone: false,
    username: false,
    school_name: false,
});

let debounceTimers = {};
function debounceCheck(field, value) {
    if (debounceTimers[field]) clearTimeout(debounceTimers[field]);
    availability.value[field] = null;
    if (!value) return;
    checking.value[field] = true;
    debounceTimers[field] = setTimeout(async () => {
        try {
            const { data } = await axios.get(route('register.availability'), {
                params: { field, value },
            });
            availability.value[field] = data.available;
        } catch (e) {
            availability.value[field] = null;
        } finally {
            checking.value[field] = false;
        }
    }, 400);
}

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" value="Phone number" />

                <TextInput
                    id="phone"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    required
                    autocomplete="tel"
                    @input="debounceCheck('phone', form.phone)"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
                <div class="mt-1 text-sm" v-if="form.phone && availability.phone !== null">
                    <span v-if="checking.phone" class="text-gray-500">Checking...</span>
                    <span v-else-if="availability.phone" class="text-green-600">Phone is available</span>
                    <span v-else class="text-red-600">Phone is already taken</span>
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="region" value="Region (Mkoa)" />

                <select
                    id="region"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    v-model="form.region"
                    required
                >
                    <option value="" disabled>Select region</option>
                    <option v-for="r in regions" :key="r" :value="r">{{ r }}</option>
                </select>

                <InputError class="mt-2" :message="form.errors.region" />
            </div>

            <div class="mt-4">
                <InputLabel for="school_name" value="School name" />

                <TextInput
                    id="school_name"
                    type="text"
                    class="mt-1 block w-full uppercase"
                    v-model="form.school_name"
                    @input="form.school_name = form.school_name?.toUpperCase(); debounceCheck('school_name', form.school_name)"
                    required
                />

                <InputError class="mt-2" :message="form.errors.school_name" />
                <div class="mt-1 text-sm" v-if="form.school_name && availability.school_name !== null">
                    <span v-if="checking.school_name" class="text-gray-500">Checking...</span>
                    <span v-else-if="availability.school_name" class="text-green-600">School name is available</span>
                    <span v-else class="text-red-600">School name is already registered</span>
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="school_number" value="School number" />

                <TextInput
                    id="school_number"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.school_number"
                    required
                />

                <InputError class="mt-2" :message="form.errors.school_number" />
            </div>

            <div class="mt-4">
                <InputLabel for="username" value="Username" />

                <TextInput
                    id="username"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.username"
                    required
                    autocomplete="username"
                    @input="debounceCheck('username', form.username)"
                />

                <InputError class="mt-2" :message="form.errors.username" />
                <div class="mt-1 text-sm" v-if="form.username && availability.username !== null">
                    <span v-if="checking.username" class="text-gray-500">Checking...</span>
                    <span v-else-if="availability.username" class="text-green-600">Username is available</span>
                    <span v-else class="text-red-600">Username is already taken</span>
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    @input="debounceCheck('email', form.email)"
                />

                <InputError class="mt-2" :message="form.errors.email" />
                <div class="mt-1 text-sm" v-if="form.email && availability.email !== null">
                    <span v-if="checking.email" class="text-gray-500">Checking...</span>
                    <span v-else-if="availability.email" class="text-green-600">Email is available</span>
                    <span v-else class="text-red-600">Email is already taken</span>
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

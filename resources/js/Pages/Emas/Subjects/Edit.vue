<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const props = defineProps({
    subject: Object,
});

const form = useForm({
    name: props.subject.name,
    code: props.subject.code,
    description: props.subject.description || '',
    status: props.subject.status,
});

const submit = () => {
    form.put(route('emas.subjects.update', props.subject.id));
};
</script>

<template>
    <Head :title="`Edit ${subject.name}`" />

    <EmasLayout>
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link 
                    :href="route('emas.subjects.index')" 
                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-white border-2 border-gray-300 text-gray-600 hover:bg-gray-50 hover:border-emerald-500 hover:text-emerald-600 transition-all"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </Link>
                <div>
                    <h1 class="text-3xl font-black text-gray-900">Edit Subject</h1>
                    <p class="text-sm text-gray-600 mt-1">Update subject information</p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="rounded-xl bg-white p-8 shadow-lg border-2 border-gray-200">
                <div class="space-y-6">
                    <!-- Subject Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                            Subject Name <span class="text-red-600">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g., Mathematics"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Subject Code -->
                    <div>
                        <label for="code" class="block text-sm font-bold text-gray-700 mb-2">
                            Subject Code <span class="text-red-600">*</span>
                        </label>
                        <input
                            id="code"
                            v-model="form.code"
                            type="text"
                            required
                            placeholder="e.g., MATH"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 uppercase"
                        />
                        <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                        <p class="mt-1 text-xs text-gray-500">Unique code to identify this subject</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            placeholder="Enter subject description..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-2">
                            Status <span class="text-red-600">*</span>
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                    <Link
                        :href="route('emas.subjects.index')"
                        class="rounded-lg border-2 border-gray-300 px-6 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 hover:bg-blue-700 px-6 py-2.5 text-white font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Updating...</span>
                        <span v-else">Update Subject</span>
                    </button>
                </div>
            </form>
        </div>
    </EmasLayout>
</template>

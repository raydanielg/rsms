<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import EmasLayout from '@/Layouts/EmasLayout.vue';

const props = defineProps({
    subjects: Object,
    filters: Object,
});

// Search and filters
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

// Watch for filter changes
watch([searchQuery, statusFilter], () => {
    router.get(route('emas.subjects.index'), {
        search: searchQuery.value,
        status: statusFilter.value,
    }, {
        preserveState: true,
        replace: true,
    });
});

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
};

// Modal state
const showModal = ref(false);
const editingSubject = ref(null);

const form = useForm({
    name: '',
    code: '',
    description: '',
    status: 'active',
});

const openCreateModal = () => {
    editingSubject.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (subject) => {
    editingSubject.value = subject;
    form.name = subject.name;
    form.code = subject.code;
    form.description = subject.description || '';
    form.status = subject.status;
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingSubject.value = null;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (editingSubject.value) {
        // Update existing subject
        form.put(route('emas.subjects.update', editingSubject.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        // Create new subject
        form.post(route('emas.subjects.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteSubject = (subject) => {
    if (confirm(`Are you sure you want to delete "${subject.name}"?`)) {
        router.delete(route('emas.subjects.destroy', subject.id));
    }
};
</script>

<template>
    <Head title="Masomo - Subjects" />

    <EmasLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900">Masomo</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage examination subjects</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-6 py-3 text-white font-bold transition-colors shadow-lg hover:shadow-xl"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Subject
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 shadow-lg">
                    <p class="text-sm font-semibold text-emerald-100">Total Subjects</p>
                    <p class="mt-2 text-4xl font-black text-white">{{ subjects.total }}</p>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 shadow-lg">
                    <p class="text-sm font-semibold text-blue-100">Active</p>
                    <p class="mt-2 text-4xl font-black text-white">{{ subjects.data.filter(s => s.status === 'active').length }}</p>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 p-6 shadow-lg">
                    <p class="text-sm font-semibold text-orange-100">Inactive</p>
                    <p class="mt-2 text-4xl font-black text-white">{{ subjects.data.filter(s => s.status === 'inactive').length }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-lg bg-white p-4 shadow-sm border border-gray-200">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search by name or code..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>
                    <div class="min-w-[150px]">
                        <select
                            v-model="statusFilter"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button
                        v-if="searchQuery || statusFilter"
                        @click="clearFilters"
                        class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Subjects Grid -->
            <div v-if="subjects.data && subjects.data.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="subject in subjects.data"
                    :key="subject.id"
                    class="group rounded-xl bg-white p-6 shadow-md border-2 border-gray-200 hover:border-emerald-500 hover:shadow-xl transition-all duration-200"
                >
                    <!-- Subject Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-black text-gray-900 group-hover:text-emerald-600 transition-colors">
                                {{ subject.name }}
                            </h3>
                            <p class="mt-1 text-sm font-bold text-emerald-600">{{ subject.code }}</p>
                        </div>
                        <span
                            :class="subject.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700'"
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold uppercase"
                        >
                            {{ subject.status }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p v-if="subject.description" class="text-sm text-gray-600 mb-4 line-clamp-2">
                        {{ subject.description }}
                    </p>
                    <p v-else class="text-sm text-gray-400 italic mb-4">No description available</p>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-4 border-t border-gray-200">
                        <button
                            @click="openEditModal(subject)"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-white font-bold transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        <button
                            @click="deleteSubject(subject)"
                            class="rounded-lg border-2 border-red-300 px-4 py-2 text-red-600 hover:bg-red-50 font-bold transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="rounded-xl bg-white p-12 text-center shadow-sm border-2 border-gray-200">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hakuna Masomo</h3>
                <p class="text-gray-600 mb-4">Start by adding your first subject</p>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-6 py-3 text-white font-bold transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add First Subject
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="subjects.data && subjects.data.length > 0" class="flex items-center justify-between rounded-lg bg-white px-6 py-4 shadow-sm border border-gray-200">
                <div class="text-sm text-gray-700">
                    Kuonyesha <span class="font-bold text-gray-900">{{ subjects.from }}</span> hadi <span class="font-bold text-gray-900">{{ subjects.to }}</span> kati ya <span class="font-bold text-gray-900">{{ subjects.total }}</span> masomo
                </div>
                <div class="flex gap-2">
                    <Link
                        v-if="subjects.prev_page_url"
                        :href="subjects.prev_page_url"
                        class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors"
                        preserve-scroll
                    >
                        Nyuma
                    </Link>
                    <span v-else class="rounded-lg border-2 border-gray-200 px-4 py-2 text-sm font-bold text-gray-400 cursor-not-allowed">
                        Nyuma
                    </span>
                    <Link
                        v-if="subjects.next_page_url"
                        :href="subjects.next_page_url"
                        class="rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors"
                        preserve-scroll
                    >
                        Mbele
                    </Link>
                    <span v-else class="rounded-lg border-2 border-gray-200 px-4 py-2 text-sm font-bold text-gray-400 cursor-not-allowed">
                        Mbele
                    </span>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Transition name="modal">
            <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

                    <!-- Modal Content -->
                    <div class="relative w-full max-w-2xl transform rounded-2xl bg-white p-8 shadow-2xl transition-all">
                        <!-- Close Button -->
                        <button
                            @click="closeModal"
                            class="absolute top-4 right-4 rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <!-- Modal Header -->
                        <div class="mb-6">
                            <h2 class="text-2xl font-black text-gray-900">
                                {{ editingSubject ? 'Edit Subject' : 'Add New Subject' }}
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ editingSubject ? 'Update subject information' : 'Create a new examination subject' }}
                            </p>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submitForm" class="space-y-5">
                            <!-- Subject Name -->
                            <div>
                                <label for="modal-name" class="block text-sm font-bold text-gray-700 mb-2">
                                    Subject Name <span class="text-red-600">*</span>
                                </label>
                                <input
                                    id="modal-name"
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
                                <label for="modal-code" class="block text-sm font-bold text-gray-700 mb-2">
                                    Subject Code <span class="text-red-600">*</span>
                                </label>
                                <input
                                    id="modal-code"
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
                                <label for="modal-description" class="block text-sm font-bold text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea
                                    id="modal-description"
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="Enter subject description..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="modal-status" class="block text-sm font-bold text-gray-700 mb-2">
                                    Status <span class="text-red-600">*</span>
                                </label>
                                <select
                                    id="modal-status"
                                    v-model="form.status"
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="rounded-lg border-2 border-gray-300 px-6 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    :class="editingSubject ? 'bg-blue-600 hover:bg-blue-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                                    class="rounded-lg px-6 py-2.5 text-white font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="form.processing">{{ editingSubject ? 'Updating...' : 'Saving...' }}</span>
                                    <span v-else>{{ editingSubject ? 'Update Subject' : 'Save Subject' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Transition>
    </EmasLayout>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: all 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.9);
    opacity: 0;
}
</style>

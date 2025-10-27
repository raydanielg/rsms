<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            users: 0,
            schools: 0,
            students: 0,
            teachers: 0
        })
    }
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Admin Dashboard
            </h2>
        </template>

        <div class="space-y-6">
            <!-- Welcome Section -->
            <div class="rounded-lg bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 p-8 text-white shadow-xl border-t-4 border-yellow-400">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-yellow-400 p-4">
                        <svg class="h-10 w-10 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">RSMS Admin Panel</h1>
                        <p class="mt-2 text-blue-100">Result Management System - Administrative Dashboard</p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Users Card -->
                <div class="rounded-lg bg-white p-6 shadow-md ring-1 ring-blue-200 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="mt-2 text-3xl font-bold text-blue-900">{{ stats.users }}</p>
                        </div>
                        <div class="rounded-full bg-gradient-to-br from-blue-100 to-blue-200 p-3">
                            <svg class="h-8 w-8 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-green-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            Active
                        </span>
                    </div>
                </div>

                <!-- Schools Card -->
                <div class="rounded-lg bg-white p-6 shadow-md ring-1 ring-yellow-200 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Schools</p>
                            <p class="mt-2 text-3xl font-bold text-blue-900">{{ stats.schools }}</p>
                        </div>
                        <div class="rounded-full bg-gradient-to-br from-yellow-100 to-yellow-200 p-3">
                            <svg class="h-8 w-8 text-yellow-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-green-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            Registered
                        </span>
                    </div>
                </div>

                <!-- Students Card -->
                <div class="rounded-lg bg-white p-6 shadow-md ring-1 ring-blue-200 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Students</p>
                            <p class="mt-2 text-3xl font-bold text-blue-900">{{ stats.students }}</p>
                        </div>
                        <div class="rounded-full bg-gradient-to-br from-blue-100 to-blue-200 p-3">
                            <svg class="h-8 w-8 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-green-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            Enrolled
                        </span>
                    </div>
                </div>

                <!-- Teachers Card -->
                <div class="rounded-lg bg-white p-6 shadow-md ring-1 ring-yellow-200 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Teachers</p>
                            <p class="mt-2 text-3xl font-bold text-blue-900">{{ stats.teachers }}</p>
                        </div>
                        <div class="rounded-full bg-gradient-to-br from-yellow-100 to-yellow-200 p-3">
                            <svg class="h-8 w-8 text-yellow-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-green-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            Active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Coming Soon Section -->
            <div class="rounded-lg bg-gradient-to-br from-white to-blue-50 p-12 text-center shadow-lg border border-blue-200">
                <div class="mx-auto max-w-2xl">
                    <div class="mb-6 inline-flex rounded-full bg-gradient-to-br from-blue-100 to-blue-200 p-6 shadow-md">
                        <svg class="h-16 w-16 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-blue-900">More Features Coming Soon</h2>
                    <p class="mt-4 text-lg text-gray-700">
                        We're working hard to bring you advanced admin features including user management, 
                        detailed analytics, system configuration, and much more.
                    </p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <div class="rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 shadow-md">
                            <p class="text-sm font-semibold text-white">User Management</p>
                        </div>
                        <div class="rounded-lg bg-gradient-to-r from-yellow-400 to-yellow-500 px-6 py-3 shadow-md">
                            <p class="text-sm font-semibold text-blue-900">Advanced Reports</p>
                        </div>
                        <div class="rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 shadow-md">
                            <p class="text-sm font-semibold text-white">System Settings</p>
                        </div>
                        <div class="rounded-lg bg-gradient-to-r from-yellow-400 to-yellow-500 px-6 py-3 shadow-md">
                            <p class="text-sm font-semibold text-blue-900">Activity Monitoring</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-lg bg-white p-6 shadow-md border border-blue-100">
                <h3 class="mb-4 text-lg font-semibold text-blue-900">Quick Actions</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <button class="flex items-center gap-3 rounded-lg border-2 border-blue-200 p-4 text-left transition hover:border-blue-400 hover:bg-blue-50 hover:shadow-md">
                        <div class="rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 p-2">
                            <svg class="h-5 w-5 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-blue-900">Add New User</p>
                            <p class="text-sm text-gray-600">Create user account</p>
                        </div>
                    </button>

                    <button class="flex items-center gap-3 rounded-lg border-2 border-yellow-200 p-4 text-left transition hover:border-yellow-400 hover:bg-yellow-50 hover:shadow-md">
                        <div class="rounded-lg bg-gradient-to-br from-yellow-100 to-yellow-200 p-2">
                            <svg class="h-5 w-5 text-yellow-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-blue-900">View Reports</p>
                            <p class="text-sm text-gray-600">System analytics</p>
                        </div>
                    </button>

                    <button class="flex items-center gap-3 rounded-lg border-2 border-blue-200 p-4 text-left transition hover:border-blue-400 hover:bg-blue-50 hover:shadow-md">
                        <div class="rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 p-2">
                            <svg class="h-5 w-5 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-blue-900">System Settings</p>
                            <p class="text-sm text-gray-600">Configure system</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

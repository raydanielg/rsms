<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const page = usePage();
const currentYear = ref(new Date().getFullYear());
const savingYear = ref(false);
const school = ref({
  name: page.props?.auth?.user?.school_name || '',
  code: page.props?.auth?.user?.school_code || '',
  address: page.props?.auth?.user?.school_address || '',
  phone: page.props?.auth?.user?.school_phone || '',
});

onMounted(() => {
  const y = page.props?.school_year;
  if (y) currentYear.value = y;
});

async function saveYear() {
  if (!currentYear.value || currentYear.value < 2000 || currentYear.value > 2100) {
    window.__toast && window.__toast('error', 'Enter a valid year (2000-2100)');
    return;
  }
  savingYear.value = true;
  try {
    const token = document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const res = await fetch(route('profile.setYear'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
      body: JSON.stringify({ year: parseInt(currentYear.value, 10) })
    });
    if (!res.ok) throw new Error('Failed');
    window.__toast && window.__toast('success', 'School year updated');
    // Soft reload to reflect year everywhere
    setTimeout(() => window.location.reload(), 400);
  } catch (e) {
    window.__toast && window.__toast('error', 'Failed to update year');
  } finally {
    savingYear.value = false;
  }
}
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Summary Card -->
                <div class="rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
                    <div class="mb-3 flex items-center justify-between">
                      <div class="text-sm font-semibold text-emerald-800">Account Overview</div>
                      <button @click="document.getElementById('edit-profile-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="inline-flex items-center gap-1 rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-emerald-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 113 3L8.25 18.1 4.5 19.5l1.4-3.75 10.962-12.263z"/></svg>
                        Edit profile
                      </button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <div class="text-xs text-gray-500">Full name</div>
                            <div class="mt-1 text-base font-semibold text-gray-900">{{ $page.props.auth.user.name }}</div>
                            <div class="text-xs text-gray-500">Email</div>
                            <div class="mt-1 text-sm text-gray-800">{{ $page.props.auth.user.email }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">School</div>
                            <div class="mt-1 text-base font-semibold text-gray-900">{{ school.name || '—' }}</div>
                            <div class="text-xs text-gray-500">Code</div>
                            <div class="mt-1 text-sm text-gray-800">{{ school.code || '—' }}</div>
                            <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                              <div>
                                <div class="text-gray-500">Phone</div>
                                <div class="mt-0.5 text-gray-800">{{ school.phone || '—' }}</div>
                              </div>
                              <div>
                                <div class="text-gray-500">Address</div>
                                <div class="mt-0.5 truncate text-gray-800" :title="school.address">{{ school.address || '—' }}</div>
                              </div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Current year</div>
                            <div class="mt-1 inline-flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-sm font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">{{ currentYear }}</span>
                                <input type="number" min="2000" max="2100" v-model.number="currentYear" class="w-24 rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                                <button @click="saveYear" :disabled="savingYear" class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-60">{{ savingYear ? 'Saving...' : 'Save' }}</button>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">This sets your working school year across the app.</div>
                        </div>
                    </div>
                </div>

                <div
                    id="edit-profile-form"
                    class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="rounded-xl border border-rose-100 bg-white p-4 shadow-sm sm:rounded-lg sm:p-8">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

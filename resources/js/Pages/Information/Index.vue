<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  tab: { type: String, default: 'announcements' },
  announcements: { type: Object, required: true },
  policies: { type: Object, required: true },
  updates: { type: Object, required: true },
});

const activeTab = ref(props.tab || 'announcements');
const lang = ref('en'); // 'en' or 'sw'
const page = usePage();
const errors = computed(() => page.props.errors || {});

function goTab(tab) {
  activeTab.value = tab;
  router.get(route('information.index'), { tab }, { preserveScroll: true, preserveState: true, replace: true });
}

// Feedback form
const fb = ref({ name: '', contact: '', message: '' });
const sending = ref(false);
async function submitFeedback() {
  sending.value = true;
  try {
    await router.post(route('information.feedback.store'), fb.value, {
      preserveScroll: true,
      onSuccess: () => { fb.value = { name: '', contact: '', message: '' }; window.__toast && window.__toast('success', lang.value==='en' ? 'Feedback sent' : 'Ujumbe umetumwa'); },
      onError: (e) => { const m = Object.values(e||{})[0] || 'Failed'; window.__toast && window.__toast('error', String(m)); }
    });
  } finally { sending.value = false; }
}

// Update reactions
const reacting = ref({});
async function reactUpdate(id, reaction) {
  reacting.value[id] = true;
  try {
    await router.post(route('information.updates.react', { id }), { reaction }, {
      preserveScroll: true,
      onSuccess: () => { window.__toast && window.__toast('success', lang.value==='en' ? 'Reaction recorded' : 'Maoni yamehifadhiwa'); },
      onError: () => { window.__toast && window.__toast('error', lang.value==='en' ? 'Failed' : 'Imeshindikana'); }
    });
  } finally { reacting.value[id] = false; }
}
</script>

<template>
  <Head title="Information" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ lang==='en' ? 'Information' : 'Taarifa' }}</h2>
        <div class="inline-flex overflow-hidden rounded-md border border-gray-200">
          <button :class="['px-3 py-1 text-sm', lang==='en' ? 'bg-green-600 text-white' : 'bg-white text-gray-700']" @click="lang='en'">EN</button>
          <button :class="['px-3 py-1 text-sm', lang==='sw' ? 'bg-green-600 text-white' : 'bg-white text-gray-700']" @click="lang='sw'">SW</button>
        </div>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Tabs -->
      <div class="rounded-xl border border-gray-100 bg-white p-2 shadow-sm">
        <nav class="flex flex-wrap gap-2">
          <button type="button" @click="goTab('announcements')" :class="['rounded-md px-3 py-2 text-sm', activeTab==='announcements' ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-50 border border-gray-200']">{{ lang==='en' ? 'Announcements' : 'Taarifa' }}</button>
          <button type="button" @click="goTab('policies')" :class="['rounded-md px-3 py-2 text-sm', activeTab==='policies' ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-50 border border-gray-200']">{{ lang==='en' ? 'Policies' : 'Sera' }}</button>
          <button type="button" @click="goTab('updates')" :class="['rounded-md px-3 py-2 text-sm', activeTab==='updates' ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-50 border border-gray-200']">{{ lang==='en' ? 'Updates' : 'Maboresho' }}</button>
          <button type="button" @click="activeTab='feedback'" :class="['rounded-md px-3 py-2 text-sm', activeTab==='feedback' ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-50 border border-gray-200']">{{ lang==='en' ? 'Feedback' : 'Maoni' }}</button>
        </nav>
      </div>

      <!-- Announcements -->
      <div v-if="activeTab==='announcements'" class="space-y-3">
        <div v-for="a in (announcements.data||[])" :key="a.id" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800">{{ lang==='en' ? a.title_en : a.title_sw }}</h3>
            <div class="text-xs text-gray-500">{{ a.published_at ? new Date(a.published_at).toLocaleDateString() : '' }}</div>
          </div>
          <p class="mt-2 whitespace-pre-wrap text-sm text-gray-700">{{ lang==='en' ? a.content_en : a.content_sw }}</p>
        </div>
        <div v-if="(announcements.data||[]).length===0" class="rounded-xl border border-dashed border-gray-200 bg-white p-6 text-center text-sm text-gray-500">{{ lang==='en' ? 'No announcements yet.' : 'Hakuna taarifa kwa sasa.' }}</div>
        <div v-if="announcements.links" class="flex justify-end gap-1">
          <button v-for="(link, i) in announcements.links" :key="i" :disabled="!link.url" @click="link.url && router.get(link.url, {}, { preserveScroll: true, preserveState: true, replace: true })" class="rounded-md px-3 py-1.5 text-sm" :class="[ link.active ? 'bg-green-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50', !link.url ? 'opacity-50' : '' ]" v-html="link.label" />
        </div>
      </div>

      <!-- Policies -->
      <div v-if="activeTab==='policies'" class="space-y-3">
        <div v-for="p in (policies.data||[])" :key="p.id" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800">{{ lang==='en' ? p.title_en : p.title_sw }}</h3>
            <div class="text-xs text-gray-500">{{ p.published_at ? new Date(p.published_at).toLocaleDateString() : '' }}</div>
          </div>
          <p class="mt-2 whitespace-pre-wrap text-sm text-gray-700">{{ lang==='en' ? p.content_en : p.content_sw }}</p>
        </div>
        <div v-if="(policies.data||[]).length===0" class="rounded-xl border border-dashed border-gray-200 bg-white p-6 text-center text-sm text-gray-500">{{ lang==='en' ? 'No policies yet.' : 'Hakuna sera kwa sasa.' }}</div>
        <div v-if="policies.links" class="flex justify-end gap-1">
          <button v-for="(link, i) in policies.links" :key="i" :disabled="!link.url" @click="link.url && router.get(link.url, {}, { preserveScroll: true, preserveState: true, replace: true })" class="rounded-md px-3 py-1.5 text-sm" :class="[ link.active ? 'bg-green-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50', !link.url ? 'opacity-50' : '' ]" v-html="link.label" />
        </div>
      </div>

      <!-- Updates -->
      <div v-if="activeTab==='updates'" class="space-y-3">
        <div v-for="u in (updates.data||[])" :key="u.id" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800">{{ lang==='en' ? u.title_en : u.title_sw }}</h3>
            <div class="text-xs text-gray-500">{{ u.published_at ? new Date(u.published_at).toLocaleDateString() : '' }}</div>
          </div>
          <p class="mt-2 whitespace-pre-wrap text-sm text-gray-700">{{ lang==='en' ? u.content_en : u.content_sw }}</p>
          <div class="mt-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-gray-600">
              <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">{{ u.accepts_count || 0 }} {{ lang==='en' ? 'accepted' : 'amekubali' }}</span>
              <span class="rounded-full bg-rose-50 px-2 py-0.5 text-rose-700">{{ u.rejects_count || 0 }} {{ lang==='en' ? 'rejected' : 'amekataa' }}</span>
            </div>
            <div class="flex gap-2">
              <button :disabled="reacting[u.id]" @click="reactUpdate(u.id, 'accepted')" type="button" class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ lang==='en' ? 'Accept' : 'Kubali' }}</button>
              <button :disabled="reacting[u.id]" @click="reactUpdate(u.id, 'rejected')" type="button" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-rose-700 disabled:opacity-60">{{ lang==='en' ? 'Reject' : 'Kataa' }}</button>
            </div>
          </div>
        </div>
        <div v-if="(updates.data||[]).length===0" class="rounded-xl border border-dashed border-gray-200 bg-white p-6 text-center text-sm text-gray-500">{{ lang==='en' ? 'No updates yet.' : 'Hakuna maboresho kwa sasa.' }}</div>
        <div v-if="updates.links" class="flex justify-end gap-1">
          <button v-for="(link, i) in updates.links" :key="i" :disabled="!link.url" @click="link.url && router.get(link.url, {}, { preserveScroll: true, preserveState: true, replace: true })" class="rounded-md px-3 py-1.5 text-sm" :class="[ link.active ? 'bg-green-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50', !link.url ? 'opacity-50' : '' ]" v-html="link.label" />
        </div>
      </div>

      <!-- Feedback -->
      <div v-if="activeTab==='feedback'" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">{{ lang==='en' ? 'Your name' : 'Jina lako' }} <span class="text-red-600">*</span></label>
            <input v-model="fb.name" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
            <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">{{ lang==='en' ? 'Contact (optional)' : 'Mawasiliano (hiari)' }}</label>
            <input v-model="fb.contact" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" />
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-medium text-gray-600">{{ lang==='en' ? 'Message' : 'Ujumbe' }} <span class="text-red-600">*</span></label>
            <textarea v-model="fb.message" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600"></textarea>
            <p v-if="errors.message" class="mt-1 text-xs text-red-600">{{ errors.message }}</p>
          </div>
        </div>
        <div class="mt-3 flex justify-end">
          <button :disabled="sending" @click="submitFeedback" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60">{{ sending ? (lang==='en' ? 'Sending...' : 'Inatuma...') : (lang==='en' ? 'Send feedback' : 'Tuma maoni') }}</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

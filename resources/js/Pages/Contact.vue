<template>
  <div class="min-h-screen bg-gradient-to-b from-white via-emerald-50 to-sky-50 antialiased">
    <Header />

    <!-- Hero -->
    <section class="relative">
      <div class="mx-auto max-w-5xl px-4 py-14 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-wide uppercase text-slate-900">Contact</h1>
        <p class="mx-auto mt-6 max-w-2xl text-slate-700 text-sm sm:text-base">Have a question, idea, or feedback? We'd love to hear from you. Fill out the form and we'll get back to you.</p>
      </div>
      <div class="pointer-events-none absolute inset-x-0 -top-6 grid grid-cols-3 opacity-50">
        <div class="h-24 blur-3xl bg-emerald-200 animate-pulse"></div>
        <div class="h-24 blur-3xl bg-sky-200 animate-pulse"></div>
        <div class="h-24 blur-3xl bg-amber-200 animate-pulse"></div>
      </div>
    </section>

    <!-- Contact Form -->
    <section class="mx-auto max-w-6xl px-4 pb-16">
      <div class="grid items-start gap-8 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700">Name</label>
              <input v-model.trim="form.name" type="text" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-slate-700">Email</label>
                <input v-model.trim="form.email" type="email" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700">Phone</label>
                <input v-model.trim="form.phone" type="number" inputmode="numeric" step="1" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700">Message</label>
              <textarea v-model.trim="form.message" rows="5" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600"></textarea>
            </div>
            <div class="flex items-center gap-3">
              <button type="submit" :disabled="loading" class="inline-flex items-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700 disabled:opacity-60">
                <svg v-if="loading" class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2" opacity="0.25"/><path d="M4 12a8 8 0 0 1 8-8" stroke-width="2"/></svg>
                Send Message
              </button>
              <p v-if="success" class="text-sm font-medium text-emerald-700">Thanks! We'll be in touch.</p>
            </div>
          </form>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="text-lg font-semibold text-slate-900">Reach us</div>
          <p class="mt-1 text-sm text-slate-700">We typically respond within 1–2 business days.</p>
          <ul class="mt-4 space-y-3 text-sm text-slate-700">
            <li class="flex items-center gap-3">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                <!-- Heroicon: Envelope -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z"/><path d="m22 8-10 6L2 8"/></svg>
              </span>
              <div class="min-w-0">
                <div class="font-medium text-slate-900">Email</div>
                <div class="truncate">info@amss.ac.tz</div>
              </div>
            </li>
            <li class="flex items-center gap-3">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                <!-- Heroicon: Phone -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v2a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.09 4.18 2 2 0 0 1 4.11 2h2a2 2 0 0 1 2 1.72c.12.9.32 1.77.59 2.6a2 2 0 0 1-.45 2.11L7.09 9.91a16 16 0 0 0 6 6l1.48-1.14a2 2 0 0 1 2.11-.45c.83.27 1.7.47 2.6.59A2 2 0 0 1 22 16.92Z"/></svg>
              </span>
              <div class="min-w-0">
                <div class="font-medium text-slate-900">Phone</div>
                <div class="truncate">+255 784 271 0054</div>
              </div>
            </li>
            <li class="flex items-center gap-3">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                <!-- Heroicon: Map Pin -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-6-4.35-6-10a6 6 0 1 1 12 0c0 5.65-6 10-6 10Z"/><circle cx="12" cy="11" r="2"/></svg>
              </span>
              <div class="min-w-0">
                <div class="font-medium text-slate-900">Address</div>
                <div class="truncate">Mwanza, Tanzania</div>
              </div>
            </li>
          </ul>
          <div class="mt-6 overflow-hidden rounded-xl border border-slate-200">
            <!-- Inline SVG illustration to avoid remote image issues -->
            <svg viewBox="0 0 600 220" class="h-48 w-full" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#34d399"/>
                  <stop offset="100%" stop-color="#0ea5e9"/>
                </linearGradient>
              </defs>
              <rect x="0" y="0" width="600" height="220" fill="url(#g1)" opacity="0.15"/>
              <g fill="#10b981" opacity="0.9">
                <circle cx="80" cy="80" r="10"/>
                <circle cx="160" cy="60" r="6"/>
                <circle cx="240" cy="120" r="8"/>
                <circle cx="320" cy="70" r="5"/>
                <circle cx="400" cy="110" r="7"/>
                <circle cx="520" cy="90" r="9"/>
              </g>
              <text x="30" y="190" fill="#0f172a" font-size="18" font-family="Inter, system-ui, Arial">Always here to help — Mwanza, Tanzania</text>
            </svg>
          </div>
          <div class="mt-6 flex items-center gap-3">
            <a href="#" class="inline-flex items-center justify-center rounded-full bg-slate-900 p-2 text-white shadow hover:bg-slate-800" aria-label="Twitter">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.8c-.7.3-1.5.5-2.3.6.8-.5 1.4-1.3 1.7-2.2-.8.5-1.7.8-2.6 1-1.6-1.7-4.3-1.6-5.8.2-1.2 1.3-1.4 3.1-.6 4.6-3.3-.2-6.4-1.8-8.4-4.4-1.1 1.9-.6 4.3 1.2 5.6-.6 0-1.1-.2-1.6-.4 0 2.1 1.4 3.9 3.4 4.3-.6.2-1.2.2-1.8.1.5 1.7 2.1 2.9 3.9 2.9-1.5 1.2-3.4 1.9-5.3 1.9H2c1.9 1.2 4.1 1.8 6.3 1.8 7.6 0 11.7-6.3 11.7-11.7v-.5c.8-.6 1.4-1.3 2-2.2z"/></svg>
            </a>
            <a href="#" class="inline-flex items-center justify-center rounded-full bg-blue-600 p-2 text-white shadow hover:bg-blue-700" aria-label="Facebook">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M15 3h4V0h-4c-2.8 0-5 2.2-5 5v3H7v4h3v12h4V12h3.1l.9-4H14V5c0-1 .8-2 1-2z"/></svg>
            </a>
            <a href="#" class="inline-flex items-center justify-center rounded-full bg-pink-600 p-2 text-white shadow hover:bg-pink-700" aria-label="Instagram">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C7.6 2 4 5.6 4 10v4c0 4.4 3.6 8 8 8h4c4.4 0 8-3.6 8-8v-4c0-4.4-3.6-8-8-8h-4zm0 2h4c3.3 0 6 2.7 6 6v4c0 3.3-2.7 6-6 6h-4c-3.3 0-6-2.7-6-6v-4c0-3.3 2.7-6 6-6zm0 2a6 6 0 1 0 0 12 6 6 0 0 0 0-12zm7-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Features & Stats -->
    <section class="mx-auto max-w-6xl px-4 pb-20">
      <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
          <div class="mb-2 text-lg font-semibold text-slate-900">Dedicated Support</div>
          <p class="text-sm text-slate-700">We offer friendly, responsive support for schools and staff.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
          <div class="mb-2 text-lg font-semibold text-slate-900">Fast Response</div>
          <p class="text-sm text-slate-700">Most messages are answered within 24–48 hours.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
          <div class="mb-2 text-lg font-semibold text-slate-900">Trusted by Schools</div>
          <p class="text-sm text-slate-700">Built with reliability, security, and ease of use in mind.</p>
        </div>
      </div>
      <div class="mt-8 grid gap-6 sm:grid-cols-3">
        <div class="rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-700 p-6 text-white shadow">
          <div class="text-3xl font-extrabold">99.9%</div>
          <div class="mt-1 text-emerald-50">Uptime</div>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-sky-600 to-sky-700 p-6 text-white shadow">
          <div class="text-3xl font-extrabold">24–48h</div>
          <div class="mt-1 text-sky-50">Avg. response</div>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-amber-600 to-amber-700 p-6 text-white shadow">
          <div class="text-3xl font-extrabold">100+</div>
          <div class="mt-1 text-amber-50">Happy users</div>
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { reactive, ref } from 'vue'

const form = reactive({ name: '', email: '', phone: '', message: '' })
const loading = ref(false)
const success = ref(false)

async function submit() {
  success.value = false
  loading.value = true
  try {
    const res = await fetch('/api/public/contact', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(form)
    })
    const data = await res.json()
    if (data && data.ok) {
      success.value = true
      form.name = ''
      form.email = ''
      form.phone = ''
      form.message = ''
    }
  } finally { loading.value = false }
}
</script>

<style scoped>
@keyframes pulsey { 0%, 100% { transform: translateY(0) } 50% { transform: translateY(-3px) } }
.animate-pulse { animation: pulsey 5s ease-in-out infinite; }
</style>

<template>
  <div class="min-h-screen bg-gradient-to-b from-emerald-50 via-white to-sky-50 antialiased">
    <Header />

    <!-- Hero -->
    <section class="relative overflow-hidden">
      <div class="mx-auto max-w-7xl px-4 py-16 sm:py-20 lg:py-24">
        <div class="grid items-center gap-8 lg:grid-cols-2">
          <div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-wide uppercase text-slate-900 leading-tight text-center">
              Empowering Schools with Beautiful, Insightful Results
            </h1>
            <p class="mt-8 text-slate-700 text-sm sm:text-base text-center">
              We build tools that make exam results simple, visual, and impactful. From class performance to student tracking—fast, elegant, and reliable.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
              <Link href="/results/schools" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700">Explore Results</Link>
              <Link href="/track" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">Track a Student</Link>
            </div>
          </div>
          <div class="relative">
            <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-emerald-200 blur-3xl opacity-50 animate-pulse"></div>
            <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-sky-200 blur-3xl opacity-50 animate-pulse"></div>
            <div class="relative rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
              <!-- Inline SVG illustration to avoid remote image issues -->
              <svg viewBox="0 0 800 420" class="w-full rounded-xl" xmlns="http://www.w3.org/2000/svg">
                <defs>
                  <linearGradient id="ga" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#34d399"/>
                    <stop offset="100%" stop-color="#0ea5e9"/>
                  </linearGradient>
                </defs>
                <rect width="800" height="420" fill="url(#ga)" opacity="0.12"/>
                <g stroke="#0ea5e9" stroke-width="2" fill="none" opacity="0.8">
                  <path d="M40 360 L140 300 L240 320 L340 250 L440 280 L540 200 L640 230 L740 160"/>
                </g>
                <g fill="#10b981" opacity="0.9">
                  <circle cx="140" cy="300" r="6"/>
                  <circle cx="240" cy="320" r="6"/>
                  <circle cx="340" cy="250" r="6"/>
                  <circle cx="440" cy="280" r="6"/>
                  <circle cx="540" cy="200" r="6"/>
                  <circle cx="640" cy="230" r="6"/>
                  <circle cx="740" cy="160" r="6"/>
                </g>
                <text x="40" y="390" fill="#0f172a" font-size="18" font-family="Inter, system-ui, Arial">Insights that matter — fast, elegant, reliable</text>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="mx-auto max-w-7xl px-4 py-12">
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
          <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm4 0h14v-2H7v2z"/></svg>
          </div>
          <div class="text-lg font-semibold text-slate-900">Clean Data Visuals</div>
          <p class="mt-1 text-sm text-slate-600">Beautiful tables and summaries with color highlights and smart layouts.</p>
        </div>
        <div class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
          <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zm0 13l-10-5v7l10 5 10-5v-7l-10 5z"/></svg>
          </div>
          <div class="text-lg font-semibold text-slate-900">Fast & Reliable</div>
          <p class="mt-1 text-sm text-slate-600">Optimized endpoints and smooth UX powered by Vue, Inertia & Laravel.</p>
        </div>
        <div class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
          <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10z"/></svg>
          </div>
          <div class="text-lg font-semibold text-slate-900">Human-centered</div>
          <p class="mt-1 text-sm text-slate-600">Designed for teachers, administrators, and parents to get insights quickly.</p>
        </div>
      </div>
    </section>

    <!-- Team / Mission -->
    <section class="mx-auto max-w-7xl px-4 py-12">
      <div class="grid items-center gap-8 lg:grid-cols-2">
        <div class="order-2 lg:order-1">
          <div class="text-2xl font-bold text-slate-900">Our Mission</div>
          <p class="mt-2 text-slate-700">To enable results transparency and performance improvement through intuitive, delightful software used across schools.</p>
          <ul class="mt-4 space-y-2 text-slate-700 text-sm">
            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Clear performance summaries</li>
            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span> Subject-level insights</li>
            <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Student-centric tracking</li>
          </ul>
        </div>
        <div class="order-1 lg:order-2">
          <div class="relative">
            <div class="absolute -top-6 -left-6 h-32 w-32 rounded-full bg-amber-200 blur-3xl opacity-60 animate-pulse"></div>
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
              <!-- Inline SVG illustration -->
              <svg viewBox="0 0 800 420" class="w-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                  <linearGradient id="gb" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#f59e0b"/>
                    <stop offset="100%" stop-color="#06b6d4"/>
                  </linearGradient>
                </defs>
                <rect width="800" height="420" fill="url(#gb)" opacity="0.12"/>
                <g>
                  <rect x="80" y="80" width="180" height="240" rx="18" fill="#0ea5e9" opacity="0.85"/>
                  <rect x="310" y="60" width="180" height="280" rx="18" fill="#10b981" opacity="0.85"/>
                  <rect x="540" y="100" width="180" height="220" rx="18" fill="#f59e0b" opacity="0.85"/>
                </g>
                <text x="40" y="380" fill="#0f172a" font-size="18" font-family="Inter, system-ui, Arial">Built with care — for schools, teachers and parents</text>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="mx-auto max-w-7xl px-4 py-12">
      <div class="rounded-2xl bg-gradient-to-r from-emerald-600 to-sky-600 p-6 text-center shadow-xl">
        <h2 class="text-xl sm:text-2xl font-bold text-white">Ready to see better results?</h2>
        <p class="mt-1 text-emerald-50">Explore results or search for a student now.</p>
        <div class="mt-4 flex flex-wrap justify-center gap-3">
          <Link href="/results/schools" class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-emerald-700 shadow hover:bg-emerald-50">View Schools</Link>
          <Link href="/track" class="rounded-xl border border-white/80 bg-transparent px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-white/10">Track Student</Link>
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import { Link } from '@inertiajs/vue3'
</script>

<style scoped>
/* subtle floating animation for hero blobs */
@keyframes floaty { 0%, 100% { transform: translateY(0) } 50% { transform: translateY(-4px) } }
.animate-pulse { animation: floaty 4s ease-in-out infinite; }
</style>

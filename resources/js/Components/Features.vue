<template>
  <section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-8">
      <div class="text-left sm:text-center">
        <h3 class="text-3xl font-bold text-gray-900">Sifa Kuu za Mfumo</h3>
        <p class="mt-4 text-gray-600">Zana zinazolingana na moduli kuu za RSMS.</p>
      </div>

      <div class="mt-12 grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <article v-for="(f,i) in features" :key="f.title" :ref="el => setItemRef(el, i)" class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm transition duration-300 hover:shadow-lg">
          <div class="absolute inset-0 bg-gradient-to-br from-green-50/0 to-green-100/0 opacity-0 transition group-hover:opacity-70"></div>
          <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-700 ring-1 ring-green-100">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" v-html="f.icon"></svg>
            </div>
            <div>
              <h4 class="text-lg font-semibold text-gray-900">{{ f.title }}</h4>
              <p class="mt-1 text-gray-600 text-sm">{{ f.desc }}</p>
            </div>
          </div>
          <div class="mt-4 flex flex-wrap gap-2">
            <span v-for="tag in f.tags" :key="tag" class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">{{ tag }}</span>
          </div>
          <div class="mt-6">
            <span class="inline-flex items-center text-sm font-medium text-green-700 transition group-hover:translate-x-0.5">
              Jifunze zaidi
              <svg class="ml-1 h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 5.5l6 6-6 6-1.5-1.5 3.9-3.9H4v-2h11.9l-3.9-3.9 1.5-1.5z"/></svg>
            </span>
          </div>
          <span class="pointer-events-none absolute -right-10 -top-10 h-24 w-24 rounded-full bg-green-100 opacity-0 blur-2xl transition group-hover:opacity-100"></span>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
const features = [
  {
    title: 'Usimamizi wa Wanafunzi',
    desc: 'Sajili, tafuta na dhibiti taarifa za wanafunzi kwa urahisi.',
    tags: ['Profiles', 'Imports', 'Exports'],
    icon: '<path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0v1H5v-1z"/>'
  },
  {
    title: 'Matokeo na Alama',
    desc: 'Ingiza alama, chakata na toa result slips haraka.',
    tags: ['Exams', 'Marks', 'Result Slips'],
    icon: '<path d="M5 3h14a2 2 0 012 2v12a2 2 0 01-2 2H9l-4 4v-4H5a2 2 0 01-2-2V5a2 2 0 012-2zm2 5h10v2H7V8zm0 4h7v2H7v-2z"/>'
  },
  {
    title: 'Ripoti za Kitaaluma',
    desc: 'Angalia takwimu na ripoti za maendeleo ya wanafunzi na madarasa.',
    tags: ['Analytics', 'Charts', 'Exports'],
    icon: '<path d="M4 19h16v2H4v-2zm2-8h3v6H6v-6zm5-4h3v10h-3V7zm5 6h3v4h-3v-4z"/>'
  },
  {
    title: 'Ratiba na Mikakati',
    desc: 'Panga mitihani na ratiba za vipindi kwa ufanisi.',
    tags: ['Timetables', 'Sitting Plan'],
    icon: '<path d="M7 2v2H5a2 2 0 00-2 2v2h18V6a2 2 0 00-2-2h-2V2h-2v2H9V2H7zm14 8H3v8a2 2 0 002 2h14a2 2 0 002-2v-8z"/>'
  },
  {
    title: 'Taarifa na Matangazo',
    desc: 'Tangaza taarifa muhimu na upokee mrejesho kutoka kwa walimu.',
    tags: ['Announcements', 'Updates', 'Feedback'],
    icon: '<path d="M3 11l9-7v14l-9-7zm12 2h6v2h-6v-2zm0-6h6v2h-6V7z"/>'
  },
  {
    title: 'Walimu',
    desc: 'Dhibiti akaunti za walimu na majukumu yao.',
    tags: ['Accounts', 'Permissions'],
    icon: '<path d="M12 12a4 4 0 100-8 4 4 0 000 8zm-7 9a7 7 0 0114 0v1H5v-1z"/>'
  },
  {
    title: 'Matokeo kwa Wilaya',
    desc: 'Linganisha na kuonyesha matokeo kwa ngazi ya wilaya na shule zote ndani yake.',
    tags: ['District', 'Comparisons', 'Analytics'],
    icon: '<path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h10v2H3v-2z"/>'
  },
  {
    title: 'Matokeo kwa Shule Moja',
    desc: 'Angalia mwenendo wa matokeo ya shule fulani kwa muhula na mwaka.',
    tags: ['School View', 'Trends'],
    icon: '<path d="M4 10l8-6 8 6v10a2 2 0 01-2 2h-4v-6H10v6H6a2 2 0 01-2-2V10z"/>'
  },
  {
    title: 'Mobile App',
    desc: 'Taarifa muhimu kupatikana kwa haraka kupitia programu ya simu (Android/iOS).',
    tags: ['Android', 'iOS', 'Push'],
    icon: '<path d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm0 4h10v12H7V6z"/>'
  },
  {
    title: 'Mzazi/Guardian Portal',
    desc: 'Wazazi kufuatilia mahudhurio, alama na tangazo moja kwa moja.',
    tags: ['Parents', 'Engagement'],
    icon: '<path d="M12 12a5 5 0 100-10 5 5 0 000 10zM4 22a8 8 0 0116 0v0.5H4V22z"/>'
  },
  {
    title: 'Usalama & Hifadhi Nakala',
    desc: 'Uthibitishaji salama, wajibu wa majukumu na hifadhi nakala otomatiki.',
    tags: ['2FA', 'RBAC', 'Backups'],
    icon: '<path d="M12 2l7 4v6c0 5-3.5 9.5-7 10-3.5-.5-7-5-7-10V6l7-4zm0 6a2 2 0 100 4 2 2 0 000-4z"/>'
  },
]

const els = ref([])
let observer
function setItemRef(el, idx) {
  if (el) {
    els.value[idx] = el
  }
}

onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible')
        observer.unobserve(entry.target)
      }
    })
  }, { threshold: 0.15 })

  els.value.forEach(el => {
    if (el) observer.observe(el)
  })
})

onBeforeUnmount(() => {
  if (observer) observer.disconnect()
})
</script>

<style scoped>
/* simple fade-up on view */
article { transform: translateY(12px); opacity: 0; }
article.is-visible { transform: translateY(0); opacity: 1; transition: all .5s ease; }
</style>

<template>
  <div class="min-h-screen bg-white antialiased">
    <Header />

    <section class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-8 py-10">
      <div class="grid gap-8 lg:grid-cols-4">

      <!-- Left Sidebar: Categories -->
      <aside class="hidden lg:block lg:col-span-1">
        <div class="sticky top-24 space-y-5">
          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900">Categories</h3>
            <ul class="mt-3 space-y-2 text-sm">
              <li v-for="cat in categories" :key="cat.name">
                <a href="#" class="flex items-center justify-between rounded-md p-2 text-gray-700 hover:bg-gray-50 hover:text-green-700">
                  <span>{{ cat.name }}</span>
                  <span class="rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-600">{{ cat.count }}</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </aside>

      <div class="lg:col-span-2">
      <!-- Breadcrumb + Back -->
      <div class="mb-6 flex items-center justify-between">
        <nav class="text-sm text-gray-600" aria-label="Breadcrumb">
          <ol class="flex items-center gap-2">
            <li><Link href="/" class="hover:text-green-700">Home</Link></li>
            <li>/</li>
            <li><Link href="/blog" class="hover:text-green-700">Blog</Link></li>
            <li>/</li>
            <li class="text-gray-900 font-medium truncate max-w-[12rem] sm:max-w-none">{{ post.title }}</li>
          </ol>
        </nav>
        <Link :href="route('blog.index')" class="inline-flex items-center gap-2 text-sm text-gray-700 hover:text-green-700">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.8l5.6-5.6L12 4l-8 8 8 8 1.4-1.4L7.8 13H20v-2z"/></svg>
          Back
        </Link>
      </div>

      <!-- Author / Meta -->
      <address class="mb-6 not-italic">
        <div class="inline-flex items-center text-sm text-gray-900">
          <img class="mr-4 h-12 w-12 rounded-full object-cover" src="/logo-trans.png" alt="Author" />
          <div>
            <a href="#" rel="author" class="text-base font-bold text-gray-900">RSMS Team</a>
            <p class="text-sm text-gray-500">Education & Product Updates</p>
            <p class="text-sm text-gray-500"><time :datetime="post.date">{{ post.date }}</time> • {{ post.read }} min read</p>
          </div>
        </div>
      </address>

      <!-- Title -->
      <h1 class="mb-4 text-3xl font-extrabold leading-tight text-gray-900 lg:mb-6 lg:text-4xl">{{ post.title }}</h1>

      <!-- Cover Image -->
      <div class="mt-6 overflow-hidden rounded-xl border bg-white shadow-sm">
        <img :src="post.image" :alt="post.title" class="h-72 w-full object-cover" />
      </div>

      <!-- Content -->
      <article class="prose prose-lg prose-green max-w-none mt-6 prose-img:rounded-lg prose-img:border">
        <p>
          {{ post.excerpt }}
        </p>
        <p>
          RSMS ni mfumo unaowezesha walimu na watendaji wa shule kusimamia alama na matokeo kwa ufanisi mkubwa. Katika makala hii, tutaangazia vipengele muhimu vya mfumo na namna vinavyosaidia kuongeza tija.
        </p>
        <img src="/68186657_56.jpg" alt="Sample" />
        <h2>Faida kuu</h2>
        <ul>
          <li>Uingizaji na uchakataji wa alama kwa haraka</li>
          <li>Ripoti na takwimu za kitaalamu</li>
          <li>Urahisi wa kutumia (UX) kwa walimu na wazazi</li>
        </ul>
        <p>
          Picha na vielelezo vinaweza kuwekwa ndani ya maudhui ya blog ili kufafanua vizuri hoja zako.
        </p>
      </article>

      <!-- Share -->
      <div class="mt-8 flex flex-wrap items-center gap-3">
        <span class="text-sm text-gray-600">Share:</span>
        <a :href="shareUrl('facebook')" target="_blank" rel="noopener" aria-label="Share on Facebook" class="inline-flex items-center justify-center rounded-md bg-blue-600/90 p-2 text-white hover:bg-blue-600">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7h-2.4V12h2.4V9.7c0-2.4 1.4-3.7 3.6-3.7 1 0 2 .2 2 .2v2.3h-1.1c-1.1 0-1.5.7-1.5 1.4V12h2.6l-.4 2.9h-2.2v7A10 10 0 0 0 22 12z"/></svg>
        </a>
        <a :href="shareUrl('twitter')" target="_blank" rel="noopener" aria-label="Share on X" class="inline-flex items-center justify-center rounded-md bg-sky-500/90 p-2 text-white hover:bg-sky-500">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.26 4.26 0 0 0 1.87-2.35 8.5 8.5 0 0 1-2.7 1.03 4.25 4.25 0 0 0-7.24 3.88A12.06 12.06 0 0 1 3.15 4.9a4.24 4.24 0 0 0 1.32 5.66 4.2 4.2 0  0 1-1.92-.53v.05a4.25 4.25 0 0 0 3.41 4.17c-.47.13-.96.2-1.47.2-.36 0-.71-.03-1.05-.1a4.26 4.26 0 0 0 3.97 2.95A8.52 8.52 0 0 1 2 19.54 12.04 12.04 0 0 0 8.29 21c7.55 0 11.68-6.26 11.68-11.68v-.53c.8-.58 1.5-1.3 2.05-2.12z"/></svg>
        </a>
        <a :href="shareUrl('whatsapp')" target="_blank" rel="noopener" aria-label="Share on WhatsApp" class="inline-flex items-center justify-center rounded-md bg-green-600/90 p-2 text-white hover:bg-green-600">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163a10 10 0 1117.566-3.32A9.93 9.93 0 0112.05 22c-1.7 0-3.29-.44-4.69-1.21L.057 24zm6.597-3.807c1.3.77 2.8 1.21 4.396 1.21 4.98 0 9.02-4.04 9.02-9.02 0-4.98-4.04-9.02-9.02-9.02-4.98 0-9.02 4.04-9.02 9.02 0 1.78.52 3.44 1.42 4.83l-.93 3.41 3.134-.91zM17.4 14.6c-.1-.16-.36-.26-.75-.46-.39-.2-2.27-1.12-2.62-1.25-.35-.13-.6-.2-.85.2-.25.4-.98 1.25-1.2 1.5-.22.25-.44.28-.83.08-.39-.2-1.64-.6-3.12-1.9-1.15-1.03-1.92-2.3-2.15-2.69-.22-.39-.02-.6.17-.8.18-.18.39-.46.58-.69.2-.22.26-.37.39-.62.13-.25.07-.47-.03-.66-.1-.2-.85-2.05-1.16-2.81-.3-.73-.6-.63-.83-.64l-.71-.01c-.24 0-.63.09-.96.46-.33.36-1.26 1.23-1.26 3 0 1.77 1.29 3.48 1.47 3.72.18.25 2.53 3.86 6.12 5.42.86.37 1.52.59 2.04.76.86.27 1.64.23 2.25.14.69-.1 2.27-.93 2.59-1.83.32-.9.32-1.67.22-1.83z"/></svg>
        </a>
        <a :href="shareUrl('linkedin')" target="_blank" rel="noopener" aria-label="Share on LinkedIn" class="inline-flex items-center justify-center rounded-md bg-sky-700/90 p-2 text-white hover:bg-sky-700">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.5 22h4V7.5h-4V22zM8 7.5V22h4v-7.5c0-2 2.5-2.2 2.5 0V22h4v-8.9c0-4.5-4.8-4.3-6.5-2.1V7.5H8z"/></svg>
        </a>
      </div>

      <Comments :post-id="post.id" />

      <!-- Social followers (bottom) -->
      <div class="mt-10 grid gap-4 sm:grid-cols-2">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
          <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7h-2.4V12h2.4V9.7c0-2.4 1.4-3.7 3.6-3.7 1 0 2 .2 2 .2v2.3h-1.1c-1.1 0-1.5.7-1.5 1.4V12h2.6l-.4 2.9h-2.2v7A10 10 0 0 0 22 12z"/></svg>
            <span class="text-sm text-gray-600">Facebook Followers</span>
          </div>
          <span class="text-lg font-semibold text-gray-900">12.4k</span>
        </div>
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
          <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-sky-500" viewBox="0 0 24 24" fill="currentColor"><path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.26 4.26 0 0 0 1.87-2.35 8.5 8.5 0 0 1-2.7 1.03 4.25 4.25 0 0 0-7.24 3.88A12.06 12.06 0 0 1 3.15 4.9a4.24 4.24 0 0 0 1.32 5.66 4.2 4.2 0 0 1-1.92-.53v.05a4.25 4.25 0 0 0 3.41 4.17c-.47.13-.96.2-1.47.2-.36 0-.71-.03-1.05-.1a4.26 4.26 0 0 0 3.97 2.95A8.52 8.52 0 0 1 2 19.54 12.04 12.04 0 0 0 8.29 21c7.55 0 11.68-6.26 11.68-11.68v-.53c.8-.58 1.5-1.3 2.05-2.12z"/></svg>
            <span class="text-sm text-gray-600">Twitter Followers</span>
          </div>
          <span class="text-lg font-semibold text-gray-900">8.1k</span>
        </div>
      </div>
      </div>

      <!-- Sidebar: Featured posts -->
      <aside class="lg:col-span-1">
        <div class="sticky top-24 space-y-5">
          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900">Featured Posts</h3>
            <div class="mt-3 space-y-3">
              <Link v-for="fp in featuredPosts" :key="fp.id" :href="route('blog.show', fp.id)" class="group flex items-center gap-3 rounded-md p-2 hover:bg-gray-50">
                <img :src="fp.image" :alt="fp.title" class="h-12 w-12 rounded object-cover" />
                <div class="min-w-0">
                  <p class="truncate text-sm font-medium text-gray-900 group-hover:text-green-700">{{ fp.title }}</p>
                  <p class="truncate text-xs text-gray-500">{{ fp.excerpt }}</p>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </aside>

      </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import Comments from '@/Components/Comments.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({ id: Number })

const demo = [
  { id: 1, title: 'Utoaji wa Result Slips kwa Haraka', excerpt: 'Jinsi RSMS inavyorahisisha utoaji wa result slips kwa sekunde chache...', image: '/hero-background.jpg', date: '2025-10-25', read: 3 },
  { id: 2, title: 'Vidokezo vya Usimamizi wa Darasa', excerpt: 'Njia bora za kukusanya na kuchanganua alama za wanafunzi...', image: '/68186657_56.jpg', date: '2025-10-24', read: 4 },
  { id: 3, title: 'Ripoti na Takwimu za Ufanisi', excerpt: 'Kutengeneza ripoti za kina kwa haraka na usahihi ndani ya RSMS...', image: '/logo-trans.png', date: '2025-10-23', read: 5 },
]

const categories = [
  { name: 'Product Updates', count: 5 },
  { name: 'Tutorials', count: 3 },
  { name: 'Education', count: 8 },
  { name: 'Community', count: 2 },
]

const featuredPosts = [
  { id: 2, title: 'Vidokezo vya Usimamizi', excerpt: 'Njia bora za kuchanganua alama...', image: '/68186657_56.jpg' },
  { id: 3, title: 'Ripoti na Takwimu', excerpt: 'Ripoti za kina haraka...', image: '/logo-trans.png' },
]

const post = computed(() => demo.find(p => p.id === props.id) || demo[0])

const page = usePage()
const currentUrl = computed(() => page.props?.ziggy?.location || (typeof window !== 'undefined' ? window.location.href : ''))

function shareUrl(type) {
  const url = encodeURIComponent(currentUrl.value || (typeof window !== 'undefined' ? window.location.href : ''))
  const text = encodeURIComponent(post.value.title)
  if (type === 'facebook') return `https://www.facebook.com/sharer/sharer.php?u=${url}`
  if (type === 'twitter') return `https://twitter.com/intent/tweet?url=${url}&text=${text}`
  if (type === 'whatsapp') return `https://wa.me/?text=${text}%20${url}`
  return '#'
}
</script>

<style>
/* Optional: Tailwind Typography class is referenced (prose). Ensure @tailwindcss/typography if you want richer styles. */
</style>

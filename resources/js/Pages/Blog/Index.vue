<template>
  <div class="min-h-screen bg-gray-50">
    <Header />

    <section class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-8 py-12">
      <div class="mb-8 text-left sm:text-center">
        <h1 class="text-3xl font-bold text-gray-900">Blog</h1>
        <p class="mt-2 text-gray-600">Habari, masasisho na maarifa kutoka RSMS.</p>
      </div>

      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <article v-for="post in posts" :key="post.id" class="overflow-hidden rounded-xl border bg-white shadow-sm transition hover:shadow-md">
          <img :src="post.image" :alt="post.title" class="h-44 w-full object-cover" />
          <div class="p-5">
            <h2 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ post.title }}</h2>
            <p class="mt-2 text-sm text-gray-600 line-clamp-3">{{ post.excerpt }}</p>
            <div class="mt-4">
              <Link :href="route('blog.show', post.id)" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700">
                View
                <svg class="ml-2 h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 5.5l6 6-6 6-1.5-1.5 3.9-3.9H4v-2h11.9l-3.9-3.9 1.5-1.5z"/></svg>
              </Link>
            </div>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import { Link } from '@inertiajs/vue3'

const posts = [
  { id: 1, title: 'Utoaji wa Result Slips kwa Haraka', excerpt: 'Jinsi RSMS inavyorahisisha utoaji wa result slips kwa sekunde chache...', image: '/hero-background.jpg' },
  { id: 2, title: 'Vidokezo vya Usimamizi wa Darasa', excerpt: 'Njia bora za kukusanya na kuchanganua alama za wanafunzi...', image: '/68186657_56.jpg' },
  { id: 3, title: 'Ripoti na Takwimu za Ufanisi', excerpt: 'Kutengeneza ripoti za kina kwa haraka na usahihi ndani ya RSMS...', image: '/logo-trans.png' },
]
</script>

<style scoped>
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>

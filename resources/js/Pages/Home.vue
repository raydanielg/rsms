<template>
  <div class="min-h-screen bg-gray-100 text-gray-800">
    <!-- Header -->
    <Header />

    <Hero />

    <Features />
    
    <!-- Blog Preview Section -->
    <section class="py-16">
      <div class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div class="text-left">
            <h3 class="text-3xl font-bold text-gray-900">Blog</h3>
            <p class="mt-2 text-gray-600">Habari na masasisho kutoka RSMS.</p>
          </div>
          <Link href="/blog" class="inline-flex items-center self-start rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
            More Blog
          </Link>
        </div>

        <div class="grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <article v-for="post in homePosts" :key="post.id" class="overflow-hidden rounded-xl border bg-white shadow-sm transition hover:shadow-md">
            <img :src="post.image" :alt="post.title" class="h-44 w-full object-cover" />
            <div class="p-5">
              <h4 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ post.title }}</h4>
              <p class="mt-2 text-sm text-gray-600 line-clamp-3">{{ post.excerpt }}</p>
              <div class="mt-4">
                <Link href="/blog" class="inline-flex items-center rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                  View
                  <svg class="ml-2 h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 5.5l6 6-6 6-1.5-1.5 3.9-3.9H4v-2h11.9l-3.9-3.9 1.5-1.5z"/></svg>
                </Link>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Hero from '@/Components/Hero.vue';
import Features from '@/Components/Features.vue';
import Footer from '@/Components/Footer.vue';

const props = defineProps({
  posts: { type: Array, default: () => [] },
});

const homePosts = props.posts;
</script>

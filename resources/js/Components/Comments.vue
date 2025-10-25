<template>
  <section class="mt-12">
    <h3 class="text-xl font-semibold text-gray-900">Maoni na Reactions</h3>

    <!-- Reactions -->
    <div class="mt-4 flex flex-wrap items-center gap-3">
      <button @click="toggleReact('like')" :class="btnClass('like')">
        👍 <span class="ml-1">{{ reactions.like }}</span>
      </button>
      <button @click="toggleReact('love')" :class="btnClass('love')">
        ❤️ <span class="ml-1">{{ reactions.love }}</span>
      </button>
      <button @click="toggleReact('clap')" :class="btnClass('clap')">
        👏 <span class="ml-1">{{ reactions.clap }}</span>
      </button>
    </div>

    <!-- New comment -->
    <form @submit.prevent="submit" class="mt-6 space-y-3">
      <div class="grid gap-3 sm:grid-cols-2">
        <input v-model.trim="form.name" type="text" placeholder="Jina lako" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-green-600 focus:outline-none" />
        <input v-model.trim="form.email" type="email" placeholder="Email (hiari)" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-green-600 focus:outline-none" />
      </div>
      <textarea v-model.trim="form.message" rows="3" placeholder="Andika maoni yako..." class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-green-600 focus:outline-none"></textarea>
      <div class="flex items-center justify-between">
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:opacity-50" :disabled="!form.message || !form.name">
          Tuma Maoni
        </button>
      </div>
    </form>

    <!-- Comments list -->
    <div class="mt-6 space-y-4">
      <div v-for="c in comments" :key="c.id" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium text-gray-900">{{ c.name }}</p>
            <p class="text-xs text-gray-500">{{ c.when }}</p>
          </div>
        </div>
        <p class="mt-2 text-gray-700">{{ c.message }}</p>
      </div>
      <p v-if="!comments.length" class="text-sm text-gray-500">Bado hakuna maoni. Kuwa wa kwanza kutoa maoni.</p>
    </div>
  </section>
</template>

<script setup>
import { reactive, ref } from 'vue'

const props = defineProps({ postId: { type: [Number,String], required: true } })

const reactions = reactive({ like: 12, love: 5, clap: 3 })
const reacted = reactive({ like: false, love: false, clap: false })
function toggleReact(key) {
  reacted[key] = !reacted[key]
  reactions[key] += reacted[key] ? 1 : -1
}
function btnClass(key) {
  return [
    'inline-flex items-center rounded-full border px-3 py-1 text-sm',
    reacted[key]
      ? 'border-green-600 bg-green-50 text-green-700'
      : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
  ]
}

const comments = ref([
  { id: 1, name: 'Mwalimu Neema', message: 'Hongera kwa kazi nzuri, RSMS imerahisisha sana kazi yetu.', when: 'saa 2 zilizopita' },
])
const form = reactive({ name: '', email: '', message: '' })
const error = ref('')

function submit() {
  error.value = ''
  if (!form.name || !form.message) {
    error.value = 'Tafadhali jaza jina na ujumbe.'
    return
  }
  comments.value.unshift({ id: Date.now(), name: form.name, message: form.message, when: 'sasa hivi' })
  form.message = ''
}
</script>

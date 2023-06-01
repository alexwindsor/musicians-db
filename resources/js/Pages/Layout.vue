<script setup>
import { Link } from '@inertiajs/vue3'
import { musoDb } from '@/musoDb.js'

</script>

<template>
  <main>
    <header class="pl-12 py-6 border-b border-black mb-5">
      <Link class="text-4xl sm:text-8xl text-center sm:text-left" :href="musoDb.base_url">Musicians DB</Link>
    </header>
    <article class="mb-12">
      <slot />
    </article>
  </main>
</template>
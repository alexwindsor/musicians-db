<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: Object
})

</script>

<template>

<div class="text-center">
    <div v-for="link in links" class="inline-block">
        <Link 
            v-if="link.url !== null"
            :href="link.url"
            class="inline-block border border-black p-2 m-2"
            :class="{
                'bg-black text-white': link.active,
                'w-10': Number(link.label) > 0
            }"
            v-html="link.label"
        ></Link>
        <div v-if="link.url === null" class="border border-black p-2 m-2 w-10">...</div>
    </div>
</div>

</template>
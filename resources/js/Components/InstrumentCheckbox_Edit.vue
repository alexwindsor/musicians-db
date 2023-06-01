<script setup>

import { musoDb } from '@/musoDb.js'

defineProps({
    id: Number,
    name: String,
    form: Object,
    instrument_checkboxes: Object
})

</script>


<template>

<label 
    class=" text-sm rounded-lg py-1 px-3 m-1 inline-block border border-black"
    :class="{
        'bg-white text-black': ! form.instrument[id],
        'bg-black text-white': form.instrument[id]
    }"
>
    {{ name }}
    <input type="checkbox" v-model="form.instrument[id]" class="px-5" :checked="form.instrument[id]">
</label>

</template>
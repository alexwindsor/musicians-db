<script setup>

import { musoDb } from '@/musoDb.js'

var props = defineProps({
    detail_types: Object,
    count: Number,
    form: Object,
    errors: Object
})

props.form.new_detail_types[props.count] = null
props.form.new_musician_detail[props.count] = null

</script>


<template>

<div class="grid grid-cols-4 gap-2">
    <div class="col-span-2 sm:col-span-1">
        <select v-model="form.new_detail_types[count]" class="border border-black rounded p-2 m-2 w-full">
            <option>Contact:</option>
            <option v-for="detail_type in detail_types" :value="detail_type.id">{{ detail_type.detail_type_text }}</option>
        </select>
        <div v-if="errors['new_detail_types.' + count]" class="text-red-500 text-xs ml-3">Please choose a contact method</div>
    </div>

    <div class="col-span-2 sm:col-span-3">
        <input type="text" v-model="form.new_musician_detail[count]" class="border border-black rounded p-2 m-2 block w-full sm:w-11/12">
        <div v-if="errors['new_musician_detail.' + count]" class="text-red-500 text-xs ml-3">Please fill this in</div>
    </div>

</div>

</template>
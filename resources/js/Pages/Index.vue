<script setup>

import Layout from '@/Pages/Layout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import { musoDb } from '@/musoDb.js'

defineProps({
    musicians: Object,
    instruments: Object,
    instruments_filter: Array,
    page: Number,
    profile_only: Boolean,
    name_search: String,
})

const querystring = window.location.search

</script>


<template>

<Head title="HOME / Musicians Database" />
<Layout>
<form :action="musoDb.base_url" method="get">
<div class="sm:grid sm:grid-cols-2 bg-gray-300 rounded m-5 p-6 gap-8">
    <div class="mb-4 sm:mb-0">
        <Link :href="musoDb.base_url + 'add_musician'" class="bg-green-500 border border-black rounded px-12 py-1 text-lg inline-block my-auto text-center text-white">Add Musician</Link>
        <br><br>

        <label for="name">Search by first or last name:</label>
        <input type="text" name="name" id="name" :value="name_search" @input="name_search = $event.target.value" class="border border-black rounded block w-full my-auto p-1">
        <br>
        <label class="mx-auto mb-8 text-sm">Only show musicians with profiles? <input type="checkbox" name="profile_only" :checked="profile_only" v-bind="profile_only"></label>
    </div>

    <div class="sm:row-span-2">
        Filter by musical instrument:
        <div class="border border-black bg-white p-1 mb-3 rounded h-48 overflow-y-scroll">
            <input type="hidden" id="instruments" name="instruments" :value="instruments_filter.join(' ')">
            <div 
                v-for="instrument in instruments" 
                :class="{
                    'bg-zinc-500 text-white': instruments_filter.includes(instrument.name.replace(' ', '_'))
                }"
                @click="musoDb.addRemoveInstrumentFilter(instruments_filter, instrument.name)"
                >
                {{ instrument.name }}
            </div>
        </div>

        <span v-for="instrument in instruments_filter" class="my-2 text-xs border border-black rounded-xl p-1 m-1 inline-block">{{ instrument.replace('_', ' ') }}</span>
    </div>

    <div class="text-center">
        <Link :href="musoDb.base_url" class="btn border border-black rounded bg-red-600 text-center py-1 mx-auto my-4 block w-2/3 text-white">RESET</Link>

        <button type="submit" class="btn border border-black rounded bg-green-600 py-1 mx-auto my-4 block w-2/3 text-white">SEARCH</button>
    </div>
</div>
</form>

<div class="sm:grid sm:grid-cols-3">
    <div class="p-5">
    <b>{{ musicians.total }}</b> musician(s) found.
    </div>

    <div v-if="musicians.total > 10" class="text-center">Page <b>{{ musicians.current_page }}</b> of {{ musicians.last_page }}</div>
</div>

<div v-for="musician in musicians.data" class="sm:grid sm:grid-cols-8 border-t border-black p-4">
    <div class="sm:col-span-3">
        <span class="text-xl">{{ musician.first_name }} {{ musician.last_name }}</span>
        <button 
            v-if="musician.profile_id" 
            class="border border-black rounded p-1 text-sm sm:float-right m-3 sm:m-5 block sm:inline"
            @click="musoDb.showProfile(musician.profile_id)"
            v-html="musoDb.show_profile == musician.profile_id ? 'HIDE PROFILE' : 'SHOW PROFILE'"
            ></button>
        <div 
            v-if="musoDb.show_profile == musician.profile_id" 
            v-html="musoDb.profile.replace(/(?:\r\n|\r|\n)/g, '<br>')"
            class="mt-5"    
        ></div>



    </div>

    <div class="sm:col-span-2 my-3 sm:my-0">
        <div v-for="instrument in musician.instruments" class="bg-black rounded-lg text-sm px-1 m-1 text-white inline-block">
            {{ instrument.name }}
        </div>
    </div>

    <div class="sm:col-span-2">
        <div v-for="n in musician.musician_details_text.length"  class="grid grid-cols-2">
            <div class="bg-gray-300 m-1 p-1 text-sm rounded">{{ musician.detail_types[n - 1] }}</div>
            <div class="bg-gray-300 m-1 p-1 text-xs overflow-x-hidden">{{ musician.musician_details_text[n - 1] }}</div>
        </div>
    </div>

    <div class="flex items-center justify-center mt-3 sm:mt-0">
        <Link :href="musoDb.base_url + 'edit_musician/' + musician.id + querystring" class="bg-blue-500 border border-black rounded text-white p-2 my-auto mx-5">Edit</Link>
        <button class="bg-red-500 border border-black rounded text-white p-2 my-auto mx-5" @click="musoDb.confirmDelete(musician.id, page, musician.first_name + ' ' + musician.last_name)">DELETE</button>
    </div>
</div>


<Pagination v-if="musicians.total > 10" :links="musicians.links"></Pagination>



</Layout>
</template>
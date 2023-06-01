<script setup>
import Layout from '@/Pages/Layout.vue'
import InstrumentCheckbox_Add from '@/Components/InstrumentCheckbox_Add.vue'
import MusicianContact_Add from '@/Components/MusicianContact_Add.vue'
import { reactive, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { musoDb } from '@/musoDb.js'

defineProps({
    instruments: Object,
    detail_types: Object,
    auth: Object,
    errors: Object
})

onMounted(() => {
    musoDb.num_of_new_contacts = 1
})

const form = reactive({
  first_name: null,
  last_name: null,
  profile: null,
  instrument: {},
  new_musician_detail: [],
  new_detail_types: []
})

function submit() {
    // remove false values from the list of selected instruments
    Object.keys(form.instrument).forEach(id => {
        if (form.instrument[id] === false) {
            delete form.instrument[id]
        }
    });

    router.post(musoDb.base_url + 'add_musician', form)
}

function deleteContactRow() {
    form.new_detail_types.pop()
    form.new_musician_detail.pop()
    musoDb.num_of_new_contacts--
}

</script>

<template>
<Head title="ADD / Musicians Database" />
<Layout>

<form @submit.prevent="submit">
<input type="hidden" name="_token" :value="auth.csrf">

<div class="sm:grid sm:grid-cols-2 bg-gray-300 rounded m-5 p-6 sm:gap-4">

    <div class="sm:col-span-2">
        <h1 class="text-xl sm:text-4xl">Add a Musician</h1>
    </div>

    <div class="mt-5">
        <input type="text" v-model="form.first_name" class="border border-black rounded p-2 block w-full sm:w-2/3" placeholder="first name">
        <div v-if="errors.first_name" class="text-red-500 text-xs">{{ errors.first_name }}</div>
    </div>

    <div class="mt-5">
        <input type="text" v-model="form.last_name" class="border border-black rounded p-2 block w-full sm:w-2/3" placeholder="last name">
        <div v-if="errors.last_name" class="text-red-500 text-xs">{{ errors.last_name }}</div>
    </div>

    <div class="sm:col-span-2 mt-5">
        <InstrumentCheckbox_Add v-for="instrument in instruments" :name="instrument.name" :id="instrument.id" :form="form" />
        <div v-if="errors.instrument" class="text-red-500 text-xs">You must select at least one instrument</div>
    </div>

    <div class="pr-3">
        <textarea v-model="form.profile" class="border border-black rounded p-2 m-2 w-full h-44" placeholder="optional profile"></textarea>
    </div>

    <div>

        <div>
            <div class="m-2">Add contact details here:</div>
            <MusicianContact_Add 
                v-for="count in musoDb.num_of_new_contacts" 
                :detail_types="detail_types" 
                :count="count - 1" 
                :form="form" 
                :errors="errors"
            />
            <div v-if="errors.new_musician_detail || errors.new_detail_types" class="text-red-500">You must add at least one method of contact here</div>
        </div>

        <button 
            class="border border-black rounded-sm p-2 m-2" 
            @click.prevent="musoDb.num_of_new_contacts++"
        >+ add</button>
        <button 
            class="border border-black rounded-sm p-2 m-2" 
            :class="{
                'border-black text-black': musoDb.num_of_new_contacts > 1,
                'border-gray-500 text-gray-500': musoDb.num_of_new_contacts === 1
            }"
            @click.prevent="deleteContactRow" 
            :disabled="musoDb.num_of_new_contacts === 1"
        >- remove</button>

    </div>

    <div class="sm:col-span-2">
        <button class="bg-green-600 text-white border border-black rounded p-2 m-2 block w-2/3 mx-auto" type="submit">ADD MUSICIAN</button>
    </div>

</div>




</form>










</Layout>

</template>
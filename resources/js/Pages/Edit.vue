<script setup>
import Layout from '@/Pages/Layout.vue'
import InstrumentCheckbox_Edit from '@/Components/InstrumentCheckbox_Edit.vue'
import MusicianContact_Add from '@/Components/MusicianContact_Add.vue'
import MusicianContact_Edit from '@/Components/MusicianContact_Edit.vue'
import { reactive, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { musoDb } from '@/musoDb.js'


const props = defineProps({
    musician: Object,
    instruments: Object,
    instrument_checkboxes: Object,
    detail_types: Object,
    errors: Object,
    auth: Object
})

onMounted(() => {
    musoDb.num_of_contacts = props.musician.musician_details_id.length
    musoDb.num_of_new_contacts = 0
})

const form = reactive({
  first_name: props.musician.first_name,
  last_name: props.musician.last_name,
  profile_text: props.musician.profile_text,
  instrument: props.instrument_checkboxes,
  musician_details_id: props.musician.musician_details_id,
  musician_detail_types_ids: props.musician.musician_detail_types_ids,
  musician_details_text: props.musician.musician_details_text,
  new_detail_types: [],
  new_musician_detail: [],
})

function deleteContactRow() {
    form.new_detail_types.pop()
    form.new_musician_detail.pop()
    musoDb.num_of_new_contacts--
}

function submit() {
    // remove false values from the list of selected instruments before submitting data to the controller
    Object.keys(form.instrument).forEach(id => {
        if (form.instrument[id] === false) {
            delete(form.instrument[id])
        } 
    });

    router.put(musoDb.base_url + 'update_musician/' + props.musician.id + '/' + window.location.search, form)
}

</script>

<template>
<Head title="EDIT / Musicians Database" />
<Layout>

<form @submit.prevent="submit">
<input type="hidden" name="_token" :value="auth.csrf">

<div class="sm:grid sm:grid-cols-2 bg-gray-300 rounded m-5 p-6 sm:gap-4">
    <div class="sm:col-span-2">
        <h1 class="text-xl sm:text-4xl">Edit {{ musician.first_name }} {{ musician.last_name }}</h1>
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
        <InstrumentCheckbox_Edit v-for="instrument in instruments" :name="instrument.name" :id="instrument.id" :form="form" :instrument_checkboxes="instrument_checkboxes" />
        <div v-if="errors.instrument" class="text-red-500 text-xs">You must select at least one instrument</div>
    </div>

    <div class="pr-3">
        <textarea v-model="form.profile_text" class="border border-black rounded p-2 m-2 w-full h-44" placeholder="optional profile"></textarea>
    </div>

    <div>
        <div>
            <MusicianContact_Edit 
                v-for="count in musoDb.num_of_contacts" 
                :detail_types="detail_types" 
                :count="count - 1" 
                :form="form" 
                :errors="errors"
                :musician_details_id="Number(musician.musician_details_id[count - 1])" 
                :musician_detail_types_id="Number(musician.musician_detail_types_ids[count - 1])"
                :musician_details_text="musician.musician_details_text[count - 1]" 
            ></MusicianContact_Edit>
        </div>

        <div>
            <MusicianContact_Add 
                v-if="musoDb.num_of_new_contacts > 0"
                v-for="n in musoDb.num_of_new_contacts" 
                :detail_types="detail_types" 
                :count="n - 1" 
                :form="form" 
                :errors="errors"
            />
        </div>

        <button 
            class="border border-black rounded-sm p-2 m-2" 
            type="button" 
            @click="musoDb.num_of_new_contacts++"
        >+ add</button>
        <button 
            class="border border-black rounded-sm p-2 m-2" 
            :class="{
                'border-black text-black': musoDb.num_of_new_contacts > 0,
                'border-gray-500 text-gray-500': musoDb.num_of_new_contacts === 0
            }"
            @click.prevent="deleteContactRow" 
            :disabled="musoDb.num_of_new_contacts === 0"
        >- remove</button>
        <br>
    </div>

    <div class="sm:col-span-2">
        <button class="bg-green-600 text-white border border-black rounded p-2 m-2 block w-2/3 mx-auto" type="submit">UPDATE {{ musician.first_name }} {{ musician.last_name }}</button>
    </div>


</div>



</form>










</Layout>

</template>
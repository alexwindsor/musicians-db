<script setup>
import { musoDb } from '@/musoDb.js'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    detail_types: Object,
    count: Number,
    form: Object,
    errors: Object,
    musician_details_id: Number,
    musician_detail_types_id: Number,
    musician_details_text: String
})

function deleteContact() {

    if (confirm('Are you sure you want to delete this contact?')) {
        props.form.musician_details_id.splice(props.count, 1)
        props.form.musician_details_text.splice(props.count, 1)
        props.form.musician_detail_types_ids.splice(props.count, 1)
        musoDb.num_of_contacts--
        router.delete(musoDb.base_url + 'delete_musician_detail/' + props.musician_details_id, { preserveScroll: true })
    }

}

</script>


<template>

<div class="grid grid-cols-7 gap-2">
    <div class="col-span-3 sm:col-span-3">
        <select v-model="form.musician_detail_types_ids[count]" class="border border-black rounded p-2 sm:m-2 w-full">
            <option 
                v-for="detail_type in detail_types" 
                :value="detail_type.id" 
                :selected="musician_detail_types_id === detail_type.id"
            >{{ detail_type.detail_type_text }}</option>
        </select>
    </div>


    <div class="col-span-4 sm:col-span-3">
        <!-- show input text if not an address -->
        <input type="text" v-if="form.musician_detail_types_ids[count] !== '4'" v-model="form.musician_details_text[count]" class="border border-black rounded p-2 sm:m-2 w-full sm:w-11/12">
        <!-- show textarea if it is an address -->
        <textarea v-if="form.musician_detail_types_ids[count] == '4'" cols="2" v-model="form.musician_details_text[count]" class="border border-black rounded p-2 sm:m-2 w-full sm:w-11/12"></textarea>
        <div v-if="errors['musician_details_text.' + count]" class="text-red-500 text-xs">Please fill this in</div>
    </div>

    <div class="col-span-7 sm:col-span-1">
        <button 
            v-if="musoDb.num_of_contacts + musoDb.num_of_new_contacts > 1"
            class="bg-red-500 border border-black rounded text-white px-2 mb-4 sm:my-4 text-sm sm:text-base" 
            type="button" 
            @click.stop.prevent="deleteContact"
        >DELETE</button>
    </div>
</div>


</template>
import { reactive } from "vue";
import { router } from '@inertiajs/vue3'

export let musoDb = reactive({

    show_profile: 0,
    profile: '',
    num_of_contacts: 1,
    num_of_new_contacts: null,
    base_url: import.meta.env.VITE_SERVER_SUBDIR,
    
    confirmDelete(id, page, name) {
        if (confirm('Are you sure you want to delete ' + name + '?')) router.delete('delete_musician/' + id + '?page=' + page)
    },

    addRemoveInstrumentFilter(instruments_filter, instrument_name) {
        if (instruments_filter.includes(instrument_name)) {
            var index = instruments_filter.indexOf(instrument_name);
            if (index !== -1) instruments_filter.splice(index, 1);
        }
        else instruments_filter.push(instrument_name)
    },

    async showProfile(profile_id) {

        this.profile = '';

        // closing profile box
        if (this.show_profile === profile_id) {
            this.show_profile = 0
        }
        // opening profile box
        else {
            this.show_profile = profile_id
            const response = await axios.post('get_profile/' + profile_id)
            this.profile = response.data
        }
    },


})
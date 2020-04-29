<template>
    <fieldset class="mb-2">
        <div class="flex flex-wrap items-end mb-1">
            <label for="id_pytlewski" class="w-full sm:w-1/2 md:w-1/4 pr-1" v-html="labels.pytlewski"></label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
                <div class="w-full sm:w-1/4 md:w-3/8 sm:pr-2 mb-1">
                    <input
                        type="text"
                        id="id_pytlewski" name="id_pytlewski"
                        autocomplete=off
                        v-model="pytlewskiSearch"
                        v-on:keyup="searchPytlewski()">
                    <!--@error('id_pytlewski')<small class="text-red-500">{{ $message }}</small>@enderror-->
                </div>
                <div class="w-full sm:w-3/4 md:w-5/8 mb-1">
                    <input
                        type="text"
                        :value="pytlewskiResult"
                        disabled>
                </div>
            </div>
        </div>
    </fieldset>



</template>

<script>
    import axios from 'axios';

    export default {
        props: {
            labels: Object,
            initialIds: Object,
        },
        data() {
            return {
                pytlewskiSearch: this.initialIds.pytlewski,
                pytlewskiResult: '←',
                // fatherSearch: this.initialIds.father,
                // fatherList: []
            }
        },
        methods: {
            searchPytlewski() {
                if(this.pytlewskiSearch != '' && this.pytlewskiSearch != null) {
                    axios
                        .post('/ajax/pytlewski', {search: this.pytlewskiSearch})
                        .then(response => {
                            this.pytlewskiResult = response.data.name
                        })
                        .catch(error => console.log(error))
                } else {
                    this.pytlewskiResult = '←'
                }
            }
        },
        mounted() {
            this.searchPytlewski()
        }
    }
</script>

<template>
    <fieldset class="mb-2">
        <div class="flex flex-wrap items-end mb-1">
            <label for="woman_id" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ labels.woman }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
                <div class="w-full sm:w-1/4 md:w-3/8 sm:pr-2 mb-1">
                    <input
                        type="text"
                        id="woman_id" name="woman_id"
                        autocomplete=off
                        v-model="womanSearch"
                        v-on:keyup="searchWoman()">
                    <!--@error('woman_id')<div class="invalid-feedback">{{ $message }}</div>@enderror-->
                </div>
                <div class="w-full sm:w-3/4 md:w-5/8 mb-1">
                    <div class="inline-block relative w-full">
                        <select id="woman_search" name="woman_search">
                            <option v-if="womanList.length == 0" disabled>←</option>
                            <option v-else v-for="person in womanList" :value="person.id">{{ person.name }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end mb-1">
            <label for="man_id" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ labels.man }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
                <div class="w-full sm:w-1/4 md:w-3/8 sm:pr-2 mb-1">
                    <input
                        type="text"
                        id="man_id" name="man_id"
                        autocomplete=off
                        v-model="manSearch"
                        v-on:keyup="searchMan()">
                    <!--@error('man_id')<div class="invalid-feedback">{{ $message }}</div>@enderror-->
                </div>
                <div class="w-full sm:w-3/4 md:w-5/8 mb-1">
                    <div class="inline-block relative w-full">
                        <select id="man_search" name="man_search">
                            <option v-if="manList.length == 0" disabled>←</option>
                            <option v-else v-for="person in manList" :value="person.id">{{ person.name }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
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
                womanSearch: this.initialIds.woman,
                womanList: [],
                manSearch: this.initialIds.man,
                manList: []
            }
        },
        methods: {
            searchWoman() {
                if(this.womanSearch != '' && this.womanSearch != null) {
                    axios
                        .post('/ajax/person', {search: this.womanSearch, sex: 'xx'})
                        .then(response => {
                            this.womanList = response.data
                        })
                        .catch(error => console.log(error))
                } else {
                    this.womanList = []
                }
            },
            searchMan() {
                if(this.manSearch != '' && this.manSearch != null) {
                    axios
                        .post('/ajax/person', {search: this.manSearch, sex: 'xy'})
                        .then(response => {
                            this.manList = response.data
                        })
                        .catch(error => console.log(error))
                } else {
                    this.manList = []
                }
            }
        },
        mounted() {
            this.searchWoman()
            this.searchMan()
        }
    }
</script>

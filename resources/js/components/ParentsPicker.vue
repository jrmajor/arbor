<template>
    <fieldset class="mb-2">
        <div class="flex flex-wrap items-end mb-1">
            <label for="mother_id" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ labels.mother }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
                <div class="w-full sm:w-1/4 md:w-3/8 sm:pr-2 mb-1">
                    <input
                        type="text"
                        id="mother_id" name="mother_id"
                        autocomplete=off
                        v-model="motherSearch"
                        v-on:keyup="searchMother()">
                    <!--@error('mother_id')<div class="invalid-feedback">{{ $message }}</div>@enderror-->
                </div>
                <div class="w-full sm:w-3/4 md:w-5/8 mb-1">
                    <div class="inline-block relative w-full">
                        <select id="mother_search" name="mother_search">
                            <option v-if="motherList.length == 0">←</option>
                            <option v-else v-for="person in motherList" :value="person.id">{{ person.name }}</option>
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
            <label for="father_id" class="w-full sm:w-1/2 md:w-1/4 pr-1 mb-1">{{ labels.father }}</label>
            <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2 flex flex-wrap">
                <div class="w-full sm:w-1/4 md:w-3/8 sm:pr-2 mb-1">
                    <input
                        type="text"
                        id="father_id" name="father_id"
                        autocomplete=off
                        v-model="fatherSearch"
                        v-on:keyup="searchFather()">
                    <!--@error('father_id')<div class="invalid-feedback">{{ $message }}</div>@enderror-->
                </div>
                <div class="w-full sm:w-3/4 md:w-5/8 mb-1">
                    <div class="inline-block relative w-full">
                        <select id="father_search" name="father_search">
                            <option v-if="fatherList.length == 0">←</option>
                            <option v-else v-for="person in fatherList" :value="person.id">{{ person.name }}</option>
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
                motherSearch: this.initialIds.mother,
                motherList: [],
                fatherSearch: this.initialIds.father,
                fatherList: []
            }
        },
        methods: {
            searchMother() {
                if(this.motherSearch != '' && this.motherSearch != null) {
                    axios
                        .post('/ajax/person', {search: this.motherSearch, sex: 'xx'})
                        .then(response => {
                            this.motherList = response.data
                        })
                        .catch(error => console.log(error))
                } else {
                    this.motherList = []
                }
            },
            searchFather() {
                if(this.fatherSearch != '' && this.fatherSearch != null) {
                    axios
                        .post('/ajax/person', {search: this.fatherSearch, sex: 'xy'})
                        .then(response => {
                            this.fatherList = response.data
                        })
                        .catch(error => console.log(error))
                } else {
                    this.fatherList = []
                }
            }
        },
        mounted() {
            this.searchMother()
            this.searchFather()
        }
    }
</script>

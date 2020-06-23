import axios from 'axios'

window.personPickerData = function (data) {
  return {
    route: data.route,
    nullable: data.nullable,
    sex: data.sex,
    initial: data.initial,
    selected: {
      id: data.initial.id,
      name: data.initial.name
    },
    open: false,
    previousSearch: '',
    search: '',
    people: [],

    findPeople(e) {
      if (e.metaKey || e.altKey) return

      if (this.selected.id == null) {
        if (this.search != this.previousSearch) {
          axios.get(this.route, {
            params: {
              sex: this.sex,
              search: this.search
            }
          })
          .then(response => {
            this.people = response.data
          })
          .catch(response => {
            console.log(response)
          })
          this.previousSearch = this.search
        }
      } else if (e.keyCode == 8) {
        this.selected.id = null
        this.selected.name = null
        this.search = ''
      } else e.preventDefault()
    },

    closeDropdown() {
      this.open = false
      if (! this.nullable && this.selected.id == null && this.initial.id != null) {
        this.selected.id = this.initial.id
        this.selected.name = this.initial.name
        this.search = null
      }
    },

    selectPerson(person) {
      this.selected.id = person.id
      this.selected.name = person.name
      this.search = null
      this.open = false
    }
  }
}

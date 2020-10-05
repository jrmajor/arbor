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
    hovered: null,
    open: false,
    shouldCloseOnBlur: true,
    previousSearch: '',
    search: '',
    people: [],

    init() {
      if (this.initial.id != null) this.people.push(this.initial)
    },

    findPeople(event) {
      if (this.selected.id != null) {
        this.search = ''
        return false
      }

      if (this.search != this.previousSearch) {
        axios.get(this.route, {
          params: {
            sex: this.sex,
            search: this.search
          }
        })
        .then(response => {
          this.people = response.data
          if (this.hovered > this.people.length - 1) this.hovered = null
        })
        .catch(response => {
          console.log(response)
        })

        this.previousSearch = this.search
      }
    },

    keydown(event) {
      if (this.selected.id == null) return

      if (event.key == 'Tab'
        || event.metaKey
        || event.ctrlKey) return

      event.preventDefault()
    },

    arrow(direction) {
      if (this.people.length == 0) return

      if (this.hovered == null) {
        return this.hovered = direction == 'up' ? this.people.length - 1 : 0
      }

      this.hovered = direction == 'up' ? this.hovered - 1 : this.hovered + 1

      if (this.hovered < 0) this.hovered = this.people.length - 1
      if (this.hovered > this.people.length - 1) this.hovered = 0
    },

    enter() {
      if (this.hovered != null) this.selectPerson(this.people[this.hovered])
    },

    closeDropdown() {
      if (! this.shouldCloseOnBlur) return this.shouldCloseOnBlur = true

      this.open = false

      if (! this.nullable && this.selected.id == null && this.initial.id != null) {
        this.selected.id = this.initial.id
        this.selected.name = this.initial.name
        this.search = null
      }

      this.hovered = null
      this.shouldCloseOnBlur = true
    },

    selectPerson(person) {
      this.selected.id = person.id
      this.selected.name = person.name
      this.search = null
      this.open = false
    },

    deselect() {
      this.selected.id = null
      this.selected.name = null
      this.search = ''
      this.open = true
    },
  }
}

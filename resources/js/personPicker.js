import route from 'ziggy-js'

window.personPickerData = function (data) {
  return {
    nullable: data.nullable,
    sex: data.sex,
    initial: data.initial,
    selected: {
      id: data.initial.id,
      name: data.initial.name,
    },
    hovered: null,
    open: false,
    shouldCloseOnBlur: true,
    previousSearch: '',
    search: '',
    people: [],

    init() {
      if (this.initial.id !== null) this.people.push(this.initial)
    },

    findPeople(event) {
      if (this.selected.id !== null) {
        this.search = ''
        return false
      }

      if (this.search !== this.previousSearch) {
        fetch(
          route('people.search', {
            sex: this.sex,
            search: this.search,
          }),
        )
          .then(response => response.json())
          .then(data => {
            this.people = data
            if (this.hovered > this.people.length - 1) this.hovered = null
          })

        this.previousSearch = this.search
      }
    },

    keydown(event) {
      if (this.selected.id === null) return

      if (event.key === 'Tab' ||
        event.metaKey ||
        event.ctrlKey) return

      event.preventDefault()
    },

    arrow(direction) {
      if (this.people.length === 0) return

      if (this.hovered === null) {
        this.hovered = direction === 'up' ? this.people.length - 1 : 0
        return
      }

      this.hovered = direction === 'up' ? this.hovered - 1 : this.hovered + 1

      if (this.hovered < 0) this.hovered = this.people.length - 1
      if (this.hovered > this.people.length - 1) this.hovered = 0
    },

    enter() {
      if (this.hovered !== null) this.selectPerson(this.people[this.hovered])
    },

    closeDropdown() {
      if (! this.shouldCloseOnBlur) {
        this.shouldCloseOnBlur = true
        return
      }

      this.open = false

      if (! this.nullable && this.selected.id === null && this.initial.id !== null) {
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

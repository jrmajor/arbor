import route from 'ziggy-js'

interface Person {
  id: number
  name: string
  dates: string | null
}

interface EmptyInitial {
  id: null
  name: null
  dates: null
}

interface Data {
  nullable: boolean
  sex: 'xy' | 'xx'
  initial: Person | EmptyInitial
}

(window as any).personPickerData = function (data: Data) {
  return {
    nullable: data.nullable,
    sex: data.sex,
    initial: data.initial,
    selected: {
      id: data.initial.id,
      name: data.initial.name,
    },
    hovered: null as number | null,
    open: false,
    shouldCloseOnBlur: true,
    previousSearch: '',
    search: '',
    people: [] as Person[],

    init() {
      if (this.initial.id !== null) this.people.push(this.initial)
    },

    findPeople() {
      if (this.selected.id !== null) {
        this.search = ''
        return
      }

      if (this.search !== this.previousSearch) {
        fetch(route('people.search', { sex: this.sex, search: this.search }))
          .then(response => response.json())
          .then(data => {
            this.people = data.people
            if ((this.hovered ?? 0) > this.people.length - 1) this.hovered = null
          })

        this.previousSearch = this.search
      }
    },

    keydown(event: KeyboardEvent) {
      if (this.selected.id === null) return

      if (event.key === 'Tab' || event.metaKey || event.ctrlKey) return

      event.preventDefault()
    },

    arrow(direction: 'up' | 'down') {
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
        this.search = ''
      }

      this.hovered = null
      this.shouldCloseOnBlur = true
    },

    selectPerson(person: Person) {
      this.selected.id = person.id
      this.selected.name = person.name
      this.search = ''
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

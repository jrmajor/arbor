import route from 'ziggy-js'

interface Person {
  id: number
  name: string
  dates: string | null
  url: string
}

export default () => ({
  hovered: null as number | null,
  open: false,
  shouldCloseOnBlur: true,
  previousSearch: '',
  search: '',
  people: [] as Person[],
  moreCount: 0,
  hiddenCount: 0,

  findPeople() {
    if (this.search === this.previousSearch) return

    fetch(route('people.search', { search: this.search }))
      .then(response => response.json())
      .then(data => {
        this.people = data.people
        this.moreCount = data.moreCount
        this.hiddenCount = data.hiddenCount

        if ((this.hovered ?? 0) > this.people.length - 1) this.hovered = null
      })

    this.previousSearch = this.search
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

  enter(event: Event) {
    this.open = false

    if (this.hovered === null) return

    event.preventDefault()
    window.location.href = this.people[this.hovered].url
  },

  closeDropdown() {
    if (! this.shouldCloseOnBlur) {
      this.shouldCloseOnBlur = true
      return
    }

    this.open = false
    this.hovered = null
    this.shouldCloseOnBlur = true
  },
})

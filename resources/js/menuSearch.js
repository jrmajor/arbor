import axios from 'axios'

window.menuSearchData = function (data) {
  return {
    route: data.route,
    hovered: null,
    open: false,
    shouldCloseOnBlur: true,
    previousSearch: '',
    search: '',
    people: [],

    findPeople(event) {
      if (this.search !== this.previousSearch) {
        axios.get(this.route, {
          params: {
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

    arrow(direction) {
      if (this.people.length === 0) return

      if (this.hovered === null) {
        return this.hovered = direction === 'up' ? this.people.length - 1 : 0
      }

      this.hovered = direction === 'up' ? this.hovered - 1 : this.hovered + 1

      if (this.hovered < 0) this.hovered = this.people.length - 1
      if (this.hovered > this.people.length - 1) this.hovered = 0
    },

    enter(event) {
      this.open = false

      if (this.hovered === null) return

      event.preventDefault()
      window.location.href = this.people[this.hovered].url
    },

    closeDropdown() {
      if (! this.shouldCloseOnBlur) return this.shouldCloseOnBlur = true

      this.open = false
      this.hovered = null
      this.shouldCloseOnBlur = true
    },
  }
}

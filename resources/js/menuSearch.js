/* global fetch */
import route from 'ziggy-js'

window.menuSearchData = function () {
  return {
    hovered: null,
    open: false,
    shouldCloseOnBlur: true,
    previousSearch: '',
    search: '',
    people: [],

    findPeople (event) {
      if (this.search !== this.previousSearch) {
        fetch(
          route('people.search', {
            search: this.search
          })
        )
          .then(response => response.json())
          .then(data => {
            this.people = data
            if (this.hovered > this.people.length - 1) this.hovered = null
          })

        this.previousSearch = this.search
      }
    },

    arrow (direction) {
      if (this.people.length === 0) return

      if (this.hovered === null) {
        this.hovered = direction === 'up' ? this.people.length - 1 : 0
        return
      }

      this.hovered = direction === 'up' ? this.hovered - 1 : this.hovered + 1

      if (this.hovered < 0) this.hovered = this.people.length - 1
      if (this.hovered > this.people.length - 1) this.hovered = 0
    },

    enter (event) {
      this.open = false

      if (this.hovered === null) return

      event.preventDefault()
      window.location.href = this.people[this.hovered].url
    },

    closeDropdown () {
      if (!this.shouldCloseOnBlur) {
        this.shouldCloseOnBlur = true
        return
      }

      this.open = false
      this.hovered = null
      this.shouldCloseOnBlur = true
    }
  }
}

import { isValid, startOfMonth, lastDayOfMonth, startOfYear, lastDayOfYear, format } from 'date-fns'

window.dateTuplePickerData = function (data) {
  return {
    simple: data.simple,
    advanced: {
      from: data.from,
      to: data.to,
    },
    dateIsValid: true,
    advancedPicker: data.advancedPicker,

    simpleChanged() {
      this.dateIsValid = true

      this.simple = this.simple.trim()

      if (this.simple.length === 0) {
        return this.advanced.from = this.advanced.to = ''
      }

      this.simple = this.simple.replace(/[^0-9-]/g, '')

      const matches = this.simple.match(/^([0-9]{4})(?:(-)(?:([0-9]{1,2})(?:(-)(?:([0-9]{1,2}))?)?)?)?$/)

      if (matches === null) return this.clearInvalidDate()

      const rawDay = matches[5],
            year = parseInt(matches[1]),
            month = parseInt(matches[3]) - 1,
            day = parseInt(matches[5]),
            secondHyphen = matches[4] === '-' ? '-' : ''

      let date

      if (! isNaN(day)) {
        if (
          isValid((date = new Date(year, month, day)))
          && date.getDate() === day && date.getMonth() === month
        ) {
          this.advanced.from = this.advanced.to = format(date, 'yyyy-MM-dd')
          if (day > 3) {
            this.simple = format(date, 'yyyy-MM-dd')
          } else {
            this.simple = format(date, 'yyyy-MM-') + rawDay
          }
          return
        }

        return this.clearInvalidDate()
      }

      if (! isNaN(month)) {
        if (
          isValid((date = new Date(year, month, 15)))
          && date.getMonth() === month
        ) {
          this.advanced.from = format(startOfMonth(date), 'yyyy-MM-dd')
          this.advanced.to = format(lastDayOfMonth(date), 'yyyy-MM-dd')
          if (month > 0 || secondHyphen === '-') {
            this.simple = format(date, 'yyyy-MM') + secondHyphen
          }
          return
        }

        return this.clearInvalidDate()
      }

      if (! isNaN(year)) {
        if (isValid((date = new Date(year, 5, 15)))) {
          this.advanced.from = format(startOfYear(date), 'yyyy-MM-dd')
          this.advanced.to = format(lastDayOfYear(date), 'yyyy-MM-dd')
          return
        }

        return this.clearInvalidDate()
      }

      this.clearInvalidDate()
    },

    simpleBlurred() {
      if (this.simple.slice(-1) === '-') this.simple = this.simple.slice(0, -1)
    },

    clearInvalidDate() {
      this.dateIsValid = false
      this.advanced.from = this.advanced.to = ''
    }
  }
}

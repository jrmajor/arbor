import { isValid, startOfMonth, lastDayOfMonth, startOfYear, lastDayOfYear, formatISO } from 'date-fns'

window.dateTuplePickerData = function (data) {
  return {
    year: data.year,
    month: data.month,
    day: data.day,
    from: data.from,
    to: data.to,
    dateIsValid: true,
    advancedPicker: data.advancedPicker,

    updateAdvanced() {
      this.dateIsValid = true

      var y = this.year.trim()
      var m = this.month.trim()
      var d = this.day.trim()

      if (y == '') {
        y = 'no year'
      } else if (parseInt(y) == y) {
        y = parseInt(y)
        if (y < 100) {
          return this.clearInvalidDate()
        }
      } else {
        return this.clearInvalidDate()
      }

      if (m == '') {
        m = 'no month'
      } else if (parseInt(m) == m) {
        m = parseInt(m) - 1
      } else {
        return this.clearInvalidDate()
      }

      if (d == '') {
        d = 'no day'
      } else if (parseInt(d) == d) {
        d = parseInt(d)
      } else {
        return this.clearInvalidDate()
      }

      var date, f, t = f = ''

      if (d != 'no day') {
        if (isValid(date = new Date(y, m, d)) && date.getDate() == d && date.getMonth() == m) {
          f = formatISO(date, { representation: 'date' })
          t = f
          console.log(y, m, d, 'full date')
        } else {
          return this.clearInvalidDate()
        }
      } else if (m != 'no month') {
        if (isValid(date = new Date(y, m, 15)) && date.getMonth() == m) {
          f = formatISO(startOfMonth(date), { representation: 'date' })
          t = formatISO(lastDayOfMonth(date), { representation: 'date' })
          console.log(y, m, d, 'no day')
        } else {
          return this.clearInvalidDate()
        }
      } else if (y != 'no year') {
        if (isValid(date = new Date(y, 5, 15))) {
          f = formatISO(startOfYear(date), { representation: 'date' })
          t = formatISO(lastDayOfYear(date), { representation: 'date' })
          console.log(y, m, d, 'no month')
        } else {
          return this.clearInvalidDate()
        }
      } else if (y == 'no year' && m == 'no month' && d == 'no day') {
        console.log(y, m, d, 'no date')
      } else {
        this.dateIsValid = false
        console.log(y, m, d, 'invalid date')
      }

      this.from = f
      this.to = t
    },

    clearInvalidDate() {
      this.dateIsValid = false
      this.from = ''
      this.to = ''
      console.log('invalid date')
    }
  }
}

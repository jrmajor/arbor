window.dateTuplePickerData = (data) => {
  return {
    year: data.year,
    month: data.month,
    day: data.day,
    from: data.from,
    to: data.to,
    dateIsValid: true,
    advancedPicker: data.advancedPicker,

    updateAdvanced() {
      y = this.year
      m = this.month
      d = this.day

      this.dateIsValid = true

      if (
        /^([12]?\d{3})$|^$/.test(y)
        && /^(0?[1-9]|1[0-2])$|^$/.test(m)
        && /^(0?[1-9]|[12]\d|3[01])$|^$/.test(d)
      ) {
        if (y != '' && m != '' && d != '') {m
          f = new Date()
          f.setUTCFullYear(y, parseInt(m)-1, d)
          f = f.toISOString().substring(0,10)
          t = f
        } else if (y != '' && m != '' && d == '') {
          f = new Date()
          t = new Date()
          f.setUTCFullYear(y, parseInt(m)-1, 1)
          t.setUTCFullYear(y, parseInt(m), 0)
          f = f.toISOString().substring(0,10)
          t = t.toISOString().substring(0,10)
        } else if (y != '' && m == '' && d == '') {
          f = new Date()
          t = new Date()
          f.setUTCFullYear(y, 0, 1)
          t.setUTCFullYear(y, 12, 0)
          f = f.toISOString().substring(0,10)
          t = t.toISOString().substring(0,10)
        } else if (y == '' && m == '' && d == '') {
          f = ''
          t = ''
        } else {
          this.dateIsValid = false
          f = ''
          t = ''
        }

        this.from = f
        this.to = t
      } else {
        this.dateIsValid = false
        this.from = ''
        this.to = ''
      }
    }
  }
}

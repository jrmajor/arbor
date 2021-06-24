import Alpine from 'alpinejs'

import menuSearch from './menuSearch'
import './personPicker'
import './dateRangePicker'

window.Alpine = Alpine

Alpine.data('menuSearch', menuSearch)

Alpine.start()

import Alpine from 'alpinejs'

import menuSearch from './menuSearch'
import './personPicker'
import './dateRangePicker'

(window as any).Alpine = Alpine

Alpine.data('menuSearch', menuSearch)

Alpine.start()

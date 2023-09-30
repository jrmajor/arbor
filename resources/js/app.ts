import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm'

import menuSearch from './menuSearch'
import './personPicker'
import './dateRangePicker'

(window as any).Alpine = Alpine

Alpine.data('menuSearch', menuSearch)

Livewire.start()

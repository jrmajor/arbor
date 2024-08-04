import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

import menuSearch from './classic/menuSearch';
import './classic/personPicker';
import './classic/dateRangePicker';

// eslint-disable-next-line @typescript-eslint/no-explicit-any
(window as any).Alpine = Alpine;

Alpine.data('menuSearch', menuSearch);

Livewire.start();

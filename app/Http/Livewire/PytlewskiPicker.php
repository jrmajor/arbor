<?php

namespace App\Http\Livewire;

use App\Services\Pytlewski\Pytlewski;
use Livewire\Component;

class PytlewskiPicker extends Component
{
    public $pytlewskiId;

    public $result;

    public function search(): string
    {
        if (blank($this->pytlewskiId)) {
            return '←';
        }

        $pytlewski = new Pytlewski($this->pytlewskiId);

        if (! $pytlewski->name && ! $pytlewski->family_name && ! $pytlewski->last_name) {
            return __('people.pytlewski.not_found');
        }

        $result = $pytlewski->name.' ';

        $result .= $pytlewski->middle_name
            ? $pytlewski->middle_name.' '
            : '';

        return $result.$pytlewski->last_name
            ? $pytlewski->last_name.' ('.$pytlewski->family_name.')'
            : $pytlewski->family_name;
    }

    public function mount($person)
    {
        $this->pytlewskiId = old('id_pytlewski') ?? $person->id_pytlewski;

        $this->result = $this->search();
    }

    public function render()
    {
        return view('livewire.pytlewski-picker');
    }
}

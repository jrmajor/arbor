<?php

namespace App\Http\Livewire;

use App\Services\Pytlewski\Pytlewski;
use Livewire\Component;

class PytlewskiPicker extends Component
{
    public $pytlewskiId;

    public $result;

    public function search()
    {
        if (blank($this->pytlewskiId)) {
            return $this->result = '←';
        }

        $pytlewski = new Pytlewski($this->pytlewskiId);

        if (! $pytlewski->name && ! $pytlewski->family_name && ! $pytlewski->last_name) {
            return $this->result = __('people.pytlewski.not_found');
        }

        $this->result = $pytlewski->name.' ';
        $this->result .= $pytlewski->middle_name ? $pytlewski->middle_name.' ' : '';
        $this->result .= $pytlewski->last_name
                            ? $pytlewski->last_name.' ('.$pytlewski->family_name.')'
                            : $pytlewski->family_name;
    }

    public function mount($person)
    {
        $this->pytlewskiId = old('id_pytlewski') ?? $person->id_pytlewski;

        $this->pytlewskiId ? $this->search() : $this->result = '←';
    }

    public function render()
    {
        return view('livewire.pytlewski-picker');
    }
}

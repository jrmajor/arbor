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

        if (blank(strip_tags($pytlewski->basic_name))) {
            return $this->result = __('people.pytlewski.not_found');
        }

        $this->result = strip_tags($pytlewski->basic_name);
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

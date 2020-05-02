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
            $this->result = 'blank';
        }
        $pytlewski = new Pytlewski($this->pytlewskiId);

        if (! $pytlewski->basic_name) {
            $this->result = __('people.pytlewski.not_found');
            return;
        }

        $this->result = strip_tags($pytlewski->basic_name);
    }

    public function mount($person)
    {
        $this->pytlewskiId = old('id_pytlewski') ?? $person->id_pytlewski;

        $this->pytlewskiId ? $this->search() : $this->result = 'x';
    }

    public function render()
    {
        return view('livewire.pytlewski-picker');
    }
}

<?php

namespace App\Http\Livewire;

use App\Services\Pytlewski\Pytlewski;
use Livewire\Component;

class PytlewskiPicker extends Component
{
    public string $pytlewskiId;

    public string $result = '';

    public function search(): string
    {
        if (blank($this->pytlewskiId)) {
            return '←';
        }

        $pytlewski = new Pytlewski((int) $this->pytlewskiId);

        if (! $pytlewski->name && ! $pytlewski->family_name && ! $pytlewski->last_name) {
            return __('people.pytlewski.not_found');
        }

        $result = $pytlewski->name . ' ';

        if ($pytlewski->middle_name) {
            $result .= $pytlewski->middle_name . ' ';
        }

        return $pytlewski->last_name
            ? $result . "{$pytlewski->last_name} ({$pytlewski->family_name})"
            : $result . $pytlewski->family_name;
    }

    // @todo Narrow $id type hint to int|null after https://github.com/livewire/livewire/issues/3375 is fixed.
    public function mount(mixed $id)
    {
        $this->pytlewskiId = (string) $id;

        $this->result = $this->search();
    }

    public function updatedPytlewskiId(): void
    {
        $this->result = $this->search();
    }

    public function render()
    {
        return view('livewire.pytlewski-picker');
    }
}

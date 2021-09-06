<?php

namespace App\Http\Livewire;

use App\Services\Pytlewski\Pytlewski;
use Illuminate\Contracts\View\View;
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

        $pytlewski = Pytlewski::find((int) $this->pytlewskiId);

        if (! $pytlewski) {
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

    public function mount(int|null $id): void
    {
        $this->pytlewskiId = (string) $id;

        $this->result = $this->search();
    }

    public function updatedPytlewskiId(): void
    {
        $this->result = $this->search();
    }

    public function render(): View
    {
        return view('livewire.pytlewski-picker');
    }
}

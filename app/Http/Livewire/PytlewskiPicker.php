<?php

namespace App\Http\Livewire;

use App\Services\Pytlewski\Pytlewski;
use App\Services\Pytlewski\PytlewskiFactory;
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

        $pytlewski = app(PytlewskiFactory::class)->find((int) $this->pytlewskiId);

        if (! $pytlewski) {
            return __('people.pytlewski.not_found');
        }

        $result = $pytlewski->name . ' ';

        if ($pytlewski->middleName) {
            $result .= $pytlewski->middleName . ' ';
        }

        return $pytlewski->lastName
            ? $result . "{$pytlewski->lastName} ({$pytlewski->familyName})"
            : $result . $pytlewski->familyName;
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

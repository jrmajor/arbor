<?php

namespace App\View\Components;

use App\Models\Person;
use Illuminate\View\Component;

class PersonPicker extends Component
{
    public ?Person $initial = null;

    public function __construct(
        public string $name,
        public string $label,
        public string $sex,
        public bool $nullable,
        ?int $initial,
    ) {
        $this->initial = $initial ? Person::find($initial) : null;
    }

    public function pickerData(): array
    {
        return [
            'nullable' => $this->nullable,
            'sex' => $this->sex,
            'initial' => [
                'id' => $this->initial?->id,
                'name' => $this->initial?->formatSimpleName(),
                'dates' => $this->initial?->formatSimpleDates(),
            ],
        ];
    }

    public function render()
    {
        return view('components.person-picker');
    }
}

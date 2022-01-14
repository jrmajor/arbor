<?php

namespace App\View\Components;

use App\Enums\Sex;
use App\Models\Person;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Js;
use Illuminate\View\Component;

class PersonPicker extends Component
{
    protected ?Person $initial = null;

    public function __construct(
        public string $name,
        public string $label,
        protected Sex $sex,
        protected bool $nullable,
        ?int $initial,
    ) {
        $this->initial = $initial ? Person::find($initial) : null;
    }

    public function pickerData(): Js
    {
        return new Js([
            'nullable' => $this->nullable,
            'sex' => $this->sex,
            'initial' => [
                'id' => $this->initial?->id,
                'name' => $this->initial?->formatSimpleName(),
                'dates' => $this->initial?->formatSimpleDates(),
            ],
        ]);
    }

    public function render(): View
    {
        return view('components.person-picker');
    }
}

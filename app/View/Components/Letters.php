<?php

namespace App\View\Components;

use App\Models\Person;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Letters extends Component
{

    public function __construct(
        public ?string $activeLetter,
        public ?string $activeType,
    ) { }

    public function letters($type): Collection
    {
        return Person::letters($type);
    }

    public function isActive($letter, $type): bool
    {
        return $letter->letter === $this->activeLetter
            && $type === $this->activeType;
    }

    public function render()
    {
        return view('components.letters');
    }
}

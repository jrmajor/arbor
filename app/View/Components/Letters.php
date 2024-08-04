<?php

namespace App\View\Components;

use App\Models\Person;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use stdClass;

class Letters extends Component
{
    public function __construct(
        public ?string $activeLetter,
        public ?string $activeType,
    ) { }

    /**
     * @param 'family'|'last' $type
     *
     * @return Collection<int, object{letter: string, total: int}>
     */
    public function letters(string $type): Collection
    {
        return Person::letters($type);
    }

    /**
     * @param 'f'|'l' $type
     */
    public function isActive(stdClass $letter, string $type): bool
    {
        return $letter->letter === $this->activeLetter
            && $type === $this->activeType;
    }

    public function render(): View
    {
        return view('components.letters');
    }
}

<?php

namespace App\Http\Livewire;

use App\Person;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PersonPicker extends Component
{
    public string $label;

    public string $name;

    public bool $nullable;

    public ?string $sex;

    public ?int $initial;

    public ?string $search;

    public function getPeople()
    {
        if (! optional(Auth::user())->canRead()) {
            return;
        }

        if (! isset($this->search) || blank($this->search)) {
            return Person::where('id', $this->initial)->get();
        }

        return Person::
            where(function ($q) {
                $q->where('id', $this->search)
                    ->orWhere(function ($q) {
                        foreach(Arr::trim(explode(' ', $this->search)) as $s) {
                            $q->where(fn ($q) =>
                                $q->whereRaw('name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('family_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('last_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                            );
                        }
                    });
            })->when(filled($this->sex), fn ($q) =>
                $q->where(fn ($q) =>
                    $q->where('sex', $this->sex)->orWhereNull('sex')
                )
            )
            ->limit(10)
            ->get();
    }

    public function mount($label, $name, $nullable, $sex, $initial)
    {
        $this->fill([
            'label' => $label,
            'name' => $name,
            'nullable' => $nullable,
            'sex' => $sex,
            'initial' => $initial,
        ]);
    }

    public function render()
    {
        return view('livewire.person-picker', [
            'people' => $this->getPeople(),
        ]);
    }
}

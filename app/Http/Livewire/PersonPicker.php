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

    public array $people = [];

    public ?string $search;

    public function search()
    {
        if (! optional(Auth::user())->canRead()) {
            return;
        }

        $this->people = Person::
            where(function ($query) {
                $query->where('id', $this->search)
                    ->orWhere(function ($query) {
                        foreach(Arr::trim(explode(' ', $this->search)) as $s) {
                            $query->where(fn($query) =>
                                $query->whereRaw('name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('family_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('last_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                            );
                        }
                    });
            })->when(filled($this->sex), fn($query) =>
                $query->where(fn($query) =>
                    $query->where('sex', $this->sex)->orWhereNull('sex')
                )
            )
            ->limit(10)
            ->get()
            ->map(fn($person) => [
                'id' => $person->id,
                'name' => $person->formatName(),
            ])->all();
    }

    public function mount($label, $name, $nullable, $sex, $initial)
    {
        $this->fill([
            'label' => $label,
            'name' => $name,
            'nullable' => $nullable,
            'sex' => $sex,
        ]);

        $person = Person::find($initial);

        if ($person) {
            $this->people[] = [
                'id' => $person->id,
                'name' => $person->formatName(),
            ];
        }
    }

    public function render()
    {
        return view('livewire.person-picker');
    }
}

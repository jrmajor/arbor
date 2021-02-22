<?php

namespace App\Http\Livewire;

use App\Models\Person;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    public $s;

    protected $queryString = [
        's' => ['except' => ''],
    ];

    public function updatingS()
    {
        $this->resetPage();
    }

    public function render()
    {
        $people = blank($this->s)
            ? collect()
            : Person::where(function ($q) {
                $q->whereKey($this->s)
                    ->orWhere(function ($q) {
                        foreach (Arr::trim(explode(' ', $this->s)) as $s) {
                            $q->where(function ($q) use ($s) {
                                return $q->whereRaw('name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('family_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('last_name collate utf8mb4_0900_ai_ci like ?', $s.'%');
                            });
                        }
                    });
            })->paginate(20);

        return view('people.search', [
            'people' => $people,
        ])->extends('layouts.app');
    }

    public function paginationView()
    {
        return 'components.pagination-links';
    }
}

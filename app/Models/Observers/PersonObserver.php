<?php

namespace App\Models\Observers;

use App\Models\Person;
use Illuminate\Support\Facades\Cache;

class PersonObserver
{
    public function created(Person $person): void
    {
        $this->clearLettersCache();
    }

    public function updated(Person $person): void
    {
        $this->clearLettersCache();
    }

    public function deleted(Person $person): void
    {
        $this->clearLettersCache();
    }

    public function restored(Person $person): void
    {
        $this->clearLettersCache();
    }

    public function forceDeleted(Person $person): void
    {
        $this->clearLettersCache();
    }

    protected function clearLettersCache(): void
    {
        Cache::forget('letters_family');
        Cache::forget('letters_last');
    }
}

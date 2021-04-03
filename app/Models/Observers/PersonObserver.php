<?php

namespace App\Models\Observers;

use App\Models\Person;
use Illuminate\Support\Facades\Cache;

class PersonObserver
{
    public function created(Person $person)
    {
        $this->clearLettersCache();
    }

    public function updated(Person $person)
    {
        $this->clearLettersCache();
    }

    public function deleted(Person $person)
    {
        $this->clearLettersCache();
    }

    public function restored(Person $person)
    {
        $this->clearLettersCache();
    }

    public function forceDeleted(Person $person)
    {
        $this->clearLettersCache();
    }

    protected function clearLettersCache()
    {
        Cache::forget('letters_family');
        Cache::forget('letters_last');
    }
}

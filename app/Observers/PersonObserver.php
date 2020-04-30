<?php

namespace App\Observers;

use App\Person;
use Illuminate\Support\Facades\Cache;

class PersonObserver
{
    /**
     * Handle the person "created" event.
     *
     * @param  \App\Person  $person
     * @return void
     */
    public function created(Person $person)
    {
        $this->clearLettersCache();
    }

    /**
     * Handle the person "updated" event.
     *
     * @param  \App\Person  $person
     * @return void
     */
    public function updated(Person $person)
    {
        $this->clearLettersCache();
    }

    /**
     * Handle the person "deleted" event.
     *
     * @param  \App\Person  $person
     * @return void
     */
    public function deleted(Person $person)
    {
        $this->clearLettersCache();
    }

    /**
     * Handle the person "restored" event.
     *
     * @param  \App\Person  $person
     * @return void
     */
    public function restored(Person $person)
    {
        $this->clearLettersCache();
    }

    /**
     * Handle the person "force deleted" event.
     *
     * @param  \App\Person  $person
     * @return void
     */
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

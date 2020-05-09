<?php

namespace App\Console\Commands;

use App\Marriage;
use App\Person;
use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class CreateMissingLogs extends Command
{
    protected $signature = 'create-missing-logs';

    protected $description = 'Create missing logs for people and marriages creation.';

    public function handle()
    {
        Person::chunk(100, function ($people) {
            $people->each(function ($person) {
                $attrs = $person->toArray();

                $sorted = [
                    'sex' => $attrs['sex'],
                    'dead' => $attrs['dead'],
                    'name' => $attrs['name'],
                    'father_id' => $attrs['father_id'],
                    'id_wielcy' => $attrs['id_wielcy'],
                    'last_name' => $attrs['last_name'],
                    'mother_id' => $attrs['mother_id'],
                    'created_at' => $attrs['created_at'],
                    'deleted_at' => $attrs['deleted_at'],
                    'updated_at' => $attrs['updated_at'],
                    'birth_place' => $attrs['birth_place'],
                    'death_cause' => $attrs['death_cause'],
                    'death_place' => $attrs['death_place'],
                    'family_name' => $attrs['family_name'],
                    'middle_name' => $attrs['middle_name'],
                    'burial_place' => $attrs['burial_place'],
                    'id_pytlewski' => $attrs['id_pytlewski'],
                    'birth_date_to' => $attrs['birth_date_to'],
                    'death_date_to' => $attrs['death_date_to'],
                    'funeral_place' => $attrs['funeral_place'],
                    'burial_date_to' => $attrs['burial_date_to'],
                    'birth_date_from' => $attrs['birth_date_from'],
                    'death_date_from' => $attrs['death_date_from'],
                    'funeral_date_to' => $attrs['funeral_date_to'],
                    'burial_date_from' => $attrs['burial_date_from'],
                    'funeral_date_from' => $attrs['funeral_date_from'],
                ];

                Activity::create([
                    'log_name' => 'people',
                    'description' => 'created',
                    'subject_id' => $person->id,
                    'subject_type' => 'App\Person',
                    'causer_id' => 1,
                    'causer_type' => 'App\User',
                    'properties' => ['attributes' => $sorted],
                    'created_at' => $person->created_at,
                    'updated_at' => $person->updated_at,
                ]);
            });
        });

        Marriage::chunk(100, function ($marriages) {
            $marriages->each(function ($marriage) {
                $attrs = $marriage->toArray();

                $sorted = [
                    'rite' => $attrs['rite'],
                    'ended' => $attrs['ended'],
                    'man_id' => $attrs['man_id'],
                    'woman_id' => $attrs['woman_id'],
                    'end_cause' => $attrs['end_cause'],
                    'man_order' => $attrs['man_order'],
                    'created_at' => $attrs['created_at'],
                    'deleted_at' => $attrs['deleted_at'],
                    'updated_at' => $attrs['updated_at'],
                    'end_date_to' => $attrs['end_date_to'],
                    'woman_order' => $attrs['woman_order'],
                    'end_date_from' => $attrs['end_date_from'],
                    'first_event_type' => $attrs['first_event_type'],
                    'first_event_place' => $attrs['first_event_place'],
                    'second_event_type' => $attrs['second_event_type'],
                    'second_event_place' => $attrs['second_event_place'],
                    'first_event_date_to' => $attrs['first_event_date_to'],
                    'second_event_date_to' => $attrs['second_event_date_to'],
                    'first_event_date_from' => $attrs['first_event_date_from'],
                    'second_event_date_from' => $attrs['second_event_date_from'],
                ];

                Activity::create([
                    'log_name' => 'marriages',
                    'description' => 'created',
                    'subject_id' => $marriage->id,
                    'subject_type' => 'App\Marriage',
                    'causer_id' => 1,
                    'causer_type' => 'App\User',
                    'properties' => ['attributes' => $sorted],
                    'created_at' => $marriage->created_at,
                    'updated_at' => $marriage->updated_at,
                ]);
            });
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMorphMap extends Migration
{
    public function up(): void
    {
        foreach ([
            'App\\Models\\Marriage' => 'marriage',
            'App\\Models\\Person' => 'person',
            'App\\Models\\User' => 'user',
        ] as $class => $name) {
            DB::table('activity_log')
                ->where('subject_type', $class)
                ->update(['subject_type' => $name]);

            DB::table('activity_log')
                ->where('causer_type', $class)
                ->update(['causer_type' => $name]);
        }
    }
}

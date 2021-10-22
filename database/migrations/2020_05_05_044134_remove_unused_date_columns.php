<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedDateColumns extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'death_date', 'funeral_date', 'burial_date']);
        });

        Schema::table('marriages', function (Blueprint $table) {
            $table->dropColumn(['first_event_date', 'second_event_date', 'end_date']);
        });
    }
}

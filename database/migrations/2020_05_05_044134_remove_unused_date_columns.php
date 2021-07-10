<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedDateColumns extends Migration
{
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('birth_date');
            $table->dropColumn('death_date');
            $table->dropColumn('funeral_date');
            $table->dropColumn('burial_date');
        });

        Schema::table('marriages', function (Blueprint $table) {
            $table->dropColumn('first_event_date');
            $table->dropColumn('second_event_date');
            $table->dropColumn('end_date');
        });
    }
}

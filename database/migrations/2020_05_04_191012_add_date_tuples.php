<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateTuples extends Migration
{
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->date('birth_date_from')->after('birth_date')->nullable();
            $table->date('birth_date_to')->after('birth_date_from')->nullable();
            $table->date('death_date_from')->after('death_date')->nullable();
            $table->date('death_date_to')->after('death_date_from')->nullable();
            $table->date('funeral_date_from')->after('funeral_date')->nullable();
            $table->date('funeral_date_to')->after('funeral_date_from')->nullable();
            $table->date('burial_date_from')->after('burial_date')->nullable();
            $table->date('burial_date_to')->after('burial_date_from')->nullable();
        });

        Schema::table('marriages', function (Blueprint $table) {
            $table->date('first_event_date_from')->after('first_event_date')->nullable();
            $table->date('first_event_date_to')->after('first_event_date_from')->nullable();
            $table->date('second_event_date_from')->after('second_event_date')->nullable();
            $table->date('second_event_date_to')->after('second_event_date_from')->nullable();
            $table->date('end_date_from')->after('end_date')->nullable();
            $table->date('end_date_to')->after('end_date_from')->nullable();
        });
    }
}

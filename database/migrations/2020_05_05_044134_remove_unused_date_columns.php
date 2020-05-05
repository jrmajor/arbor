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

    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->string('birth_date', 10)->nullable();
            $table->string('death_date', 10)->nullable();
            $table->string('funeral_date', 10)->nullable();
            $table->string('burial_date', 10)->nullable();
        });

        Schema::table('marriages', function (Blueprint $table) {
            $table->string('first_event_date', 10)->nullable();
            $table->string('second_event_date', 10)->nullable();
            $table->string('end_date', 10)->nullable();
        });
    }
}

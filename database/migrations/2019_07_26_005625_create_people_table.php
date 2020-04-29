<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleTable extends Migration
{
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id('id');
            $table->string('id_wielcy', 20)->nullable();
            $table->unsignedMediumInteger('id_pytlewski')->nullable();

            $table->enum('sex', ['xy', 'xx'])->nullable();
            $table->string('name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('family_name', 100);
            $table->string('last_name', 100)->nullable();

            $table->foreignId('mother_id')->nullable()
                ->references('id')->on('people');
            $table->foreignId('father_id')->nullable()
                ->references('id')->on('people');

            $table->string('birth_date', 10)->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->boolean('dead')->default(false);
            $table->string('death_date', 10)->nullable();
            $table->string('death_place', 100)->nullable();
            $table->string('death_cause', 100)->nullable();
            $table->string('funeral_date', 10)->nullable();
            $table->string('funeral_place', 100)->nullable();
            $table->string('burial_date', 10)->nullable();
            $table->string('burial_place', 100)->nullable();

            $table->unsignedTinyInteger('visibility')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('id_pytlewski');
            $table->index('mother_id');
            $table->index('father_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('people');
    }
}

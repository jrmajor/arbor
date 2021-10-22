<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarriagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('marriages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('woman_id');
            $table->integer('woman_order')->nullable();
            $table->unsignedBigInteger('man_id');
            $table->integer('man_order')->nullable();
            $table->string('rite', 50)->nullable();
            $table->string('first_event_type', 50)->nullable();
            $table->string('first_event_date', 10)->nullable();
            $table->string('first_event_place', 100)->nullable();
            $table->string('second_event_type', 50)->nullable();
            $table->string('second_event_date', 10)->nullable();
            $table->string('second_event_place', 100)->nullable();
            $table->boolean('ended')->default(false);
            $table->string('end_cause', 100)->nullable();
            $table->string('end_date', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('woman_id');
            $table->index('man_id');
            $table->index(['woman_id', 'man_id'], 'pair');
        });
    }
}

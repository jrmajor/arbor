<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeFromIntToBool extends Migration
{
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->boolean('visibility')->default(false)->change();
        });
    }
}

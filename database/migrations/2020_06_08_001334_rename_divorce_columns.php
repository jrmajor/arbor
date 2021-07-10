<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDivorceColumns extends Migration
{
    public function up()
    {
        Schema::table('marriages', function (Blueprint $table) {
            $table->renameColumn('ended', 'divorced');
            $table->renameColumn('end_date_from', 'divorce_date_from');
            $table->renameColumn('end_date_to', 'divorce_date_to');
        });

        Schema::table('marriages', function (Blueprint $table) {
            $table->string('divorce_place', 100)->nullable()->after('divorce_date_to');
        });
    }
}

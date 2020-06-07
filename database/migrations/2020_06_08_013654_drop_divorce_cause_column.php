<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDivorceCauseColumn extends Migration
{
    public function up()
    {
        Schema::table('marriages', function (Blueprint $table) {
            $table->dropColumn('end_cause');
        });
    }

    public function down()
    {
        Schema::table('marriages', function (Blueprint $table) {
            $table->string('end_cause', 100)->nullable()->after('divorced');
        });
    }
}

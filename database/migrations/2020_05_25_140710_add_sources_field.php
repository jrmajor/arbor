<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourcesField extends Migration
{
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->json('sources')->nullable()->after('burial_place');
        });
    }

    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('sources');
        });
    }
}

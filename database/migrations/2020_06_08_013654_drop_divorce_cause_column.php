<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDivorceCauseColumn extends Migration
{
    public function up(): void
    {
        Schema::table('marriages', function (Blueprint $table) {
            $table->dropColumn('end_cause');
        });
    }
}

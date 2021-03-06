<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateMorphsToNewModelsDirectory extends Migration
{
    public function up()
    {
        DB::table('activity_log')
            ->where('causer_type', 'App\\User')
            ->update(['causer_type' => 'App\\Models\\User']);

        DB::table('activity_log')
            ->where('subject_type', 'App\\User')
            ->update(['subject_type' => 'App\\Models\\User']);

        DB::table('activity_log')
            ->where('subject_type', 'App\\Person')
            ->update(['subject_type' => 'App\\Models\\Person']);

        DB::table('activity_log')
            ->where('subject_type', 'App\\Marriage')
            ->update(['subject_type' => 'App\\Models\\Marriage']);
    }

    public function down()
    {
        DB::table('activity_log')
            ->where('causer_type', 'App\\Models\\User')
            ->update(['causer_type' => 'App\\User']);

        DB::table('activity_log')
            ->where('subject_type', 'App\\Models\\User')
            ->update(['subject_type' => 'App\\User']);

        DB::table('activity_log')
            ->where('subject_type', 'App\\Models\\Person')
            ->update(['subject_type' => 'App\\Person']);

        DB::table('activity_log')
            ->where('subject_type', 'App\\Models\\Marriage')
            ->update(['subject_type' => 'App\\Marriage']);
    }
}

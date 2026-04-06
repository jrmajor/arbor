<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->json('attribute_changes')->nullable()->after('causer_id');
            $table->dropColumn('batch_uuid');
        });

        DB::table('activity_log')->whereNotNull('properties')->eachById(function ($row) {
            $properties = json_decode($row->properties, true) ?? [];
            $changes = array_intersect_key($properties, array_flip(['attributes', 'old', 'new']));
            $remaining = array_diff_key($properties, array_flip(['attributes', 'old', 'new']));

            if (in_array($row->description, ['added-biography', 'updated-biography', 'deleted-biography'])) {
                $changes = [
                    'attributes' => ['biography' => $changes['new']],
                    'old' => ['biography' => $changes['old']],
                ];
            }

            DB::table('activity_log')->where('id', $row->id)->update([
                'properties' => $remaining ? json_encode($remaining) : null,
                'attribute_changes' => $changes ? json_encode($changes) : null,
            ]);
        });
    }
};

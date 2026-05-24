<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'venue')) {
                $table->string('venue', 255)->nullable()->after('description');
            }
            if (!Schema::hasColumn('events', 'venue_url')) {
                $table->string('venue_url', 500)->nullable()->after('venue');
            }
            if (!Schema::hasColumn('events', 'start_time')) {
                $table->time('start_time')->nullable()->after('venue_url');
            }
            if (!Schema::hasColumn('events', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('events', 'creator_ig')) {
                $table->string('creator_ig', 100)->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('events', 'sponsors')) {
                $table->json('sponsors')->nullable()->after('creator_ig');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $cols = ['venue', 'venue_url', 'start_time', 'end_time', 'creator_ig', 'sponsors'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

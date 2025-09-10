<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('feature_requests', function (Blueprint $table) {
            // Add up_votes and down_votes columns if they don't exist
            if (!Schema::hasColumn('feature_requests', 'up_votes')) {
                $table->integer('up_votes')->default(0)->after('vote_count');
            }
            
            if (!Schema::hasColumn('feature_requests', 'down_votes')) {
                $table->integer('down_votes')->default(0)->after('up_votes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_requests', function (Blueprint $table) {
            if (Schema::hasColumn('feature_requests', 'up_votes')) {
                $table->dropColumn('up_votes');
            }
            
            if (Schema::hasColumn('feature_requests', 'down_votes')) {
                $table->dropColumn('down_votes');
            }
        });
    }
};
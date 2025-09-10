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
            if (!Schema::hasColumn('feature_requests', 'additional_info')) {
                $table->text('additional_info')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_requests', function (Blueprint $table) {
            if (Schema::hasColumn('feature_requests', 'additional_info')) {
                $table->dropColumn('additional_info');
            }
        });
    }
};

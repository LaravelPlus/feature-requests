<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

// The model sets and queries `uuid`, but no earlier migration creates it (fresh installs break).
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('feature_requests', 'uuid')) {
            return;
        }

        Schema::table('feature_requests', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        DB::table('feature_requests')->whereNull('uuid')->orderBy('id')->each(
            fn ($row) => DB::table('feature_requests')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()])
        );

        Schema::table('feature_requests', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('feature_requests', 'uuid')) {
            Schema::table('feature_requests', function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};

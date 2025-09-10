<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add remaining missing columns
        Schema::table('feature_requests', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('feature_requests', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('feature_requests', 'due_date')) {
                $table->date('due_date')->nullable()->after('assigned_to');
            }
            if (!Schema::hasColumn('feature_requests', 'estimated_effort')) {
                $table->integer('estimated_effort')->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('feature_requests', 'tags')) {
                $table->json('tags')->nullable()->after('estimated_effort');
            }
            if (!Schema::hasColumn('feature_requests', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('tags');
            }
            if (!Schema::hasColumn('feature_requests', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_public');
            }
            if (!Schema::hasColumn('feature_requests', 'vote_count')) {
                $table->integer('vote_count')->default(0)->after('is_featured');
            }
            if (!Schema::hasColumn('feature_requests', 'comment_count')) {
                $table->integer('comment_count')->default(0)->after('vote_count');
            }
            if (!Schema::hasColumn('feature_requests', 'view_count')) {
                $table->integer('view_count')->default(0)->after('comment_count');
            }
            if (!Schema::hasColumn('feature_requests', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });

        // Update existing data with default values
        DB::table('feature_requests')->update([
            'is_public' => true,
            'is_featured' => false,
            'comment_count' => 0,
            'view_count' => 0,
        ]);

        // Rename votes column to vote_count if it exists
        if (Schema::hasColumn('feature_requests', 'votes') && !Schema::hasColumn('feature_requests', 'vote_count')) {
            Schema::table('feature_requests', function (Blueprint $table) {
                $table->renameColumn('votes', 'vote_count');
            });
        }

        // Drop the old status_id column and foreign key if they exist
        if (Schema::hasColumn('feature_requests', 'status_id')) {
            Schema::table('feature_requests', function (Blueprint $table) {
                try {
                    $table->dropForeign('feature_requests_ibfk_1');
                } catch (Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('status_id');
            });
        }

        // Add foreign key constraints if they don't exist
        Schema::table('feature_requests', function (Blueprint $table) {
            try {
                $table->foreign('category_id')->references('id')->on('feature_request_categories')->onDelete('set null');
            } catch (Exception $e) {
                // Foreign key might already exist
            }
            try {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            } catch (Exception $e) {
                // Foreign key might already exist
            }
            try {
                $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            } catch (Exception $e) {
                // Foreign key might already exist
            }
        });

        // Add indexes if they don't exist
        Schema::table('feature_requests', function (Blueprint $table) {
            try {
                $table->index(['status', 'created_at']);
            } catch (Exception $e) {
                // Index might already exist
            }
            try {
                $table->index(['category_id', 'status']);
            } catch (Exception $e) {
                // Index might already exist
            }
            try {
                $table->index(['user_id', 'created_at']);
            } catch (Exception $e) {
                // Index might already exist
            }
            try {
                $table->index(['assigned_to', 'status']);
            } catch (Exception $e) {
                // Index might already exist
            }
            try {
                $table->index(['is_public', 'status']);
            } catch (Exception $e) {
                // Index might already exist
            }
            try {
                $table->index(['is_featured', 'created_at']);
            } catch (Exception $e) {
                // Index might already exist
            }
            try {
                $table->index(['vote_count', 'created_at']);
            } catch (Exception $e) {
                // Index might already exist
            }
            try {
                $table->index(['due_date', 'status']);
            } catch (Exception $e) {
                // Index might already exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_requests', function (Blueprint $table) {
            // Drop indexes
            try {
                $table->dropIndex(['status', 'created_at']);
                $table->dropIndex(['category_id', 'status']);
                $table->dropIndex(['user_id', 'created_at']);
                $table->dropIndex(['assigned_to', 'status']);
                $table->dropIndex(['is_public', 'status']);
                $table->dropIndex(['is_featured', 'created_at']);
                $table->dropIndex(['vote_count', 'created_at']);
                $table->dropIndex(['due_date', 'status']);
            } catch (Exception $e) {
                // Indexes might not exist
            }

            // Drop foreign keys
            try {
                $table->dropForeign(['category_id']);
                $table->dropForeign(['user_id']);
                $table->dropForeign(['assigned_to']);
            } catch (Exception $e) {
                // Foreign keys might not exist
            }

            // Drop added columns
            $table->dropColumn([
                'assigned_to',
                'due_date',
                'estimated_effort',
                'tags',
                'is_public',
                'is_featured',
                'comment_count',
                'view_count',
                'deleted_at'
            ]);

            // Rename vote_count back to votes
            if (Schema::hasColumn('feature_requests', 'vote_count')) {
                $table->renameColumn('vote_count', 'votes');
            }
        });

        // Re-add status_id column
        Schema::table('feature_requests', function (Blueprint $table) {
            $table->bigInteger('status_id')->unsigned()->after('description');
            $table->foreign('status_id')->references('id')->on('feature_statuses');
        });
    }
};

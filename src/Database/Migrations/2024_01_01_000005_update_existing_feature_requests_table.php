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
        // First, add columns without foreign key constraints
        Schema::table('feature_requests', function (Blueprint $table) {
            // Add missing columns
            $table->string('slug')->nullable()->after('title');
            $table->enum('status', [
                'pending',
                'under_review',
                'planned',
                'in_progress',
                'completed',
                'rejected'
            ])->default('pending')->after('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->after('status');
            $table->unsignedBigInteger('category_id')->nullable()->after('priority');
            $table->unsignedBigInteger('user_id')->nullable()->after('category_id');
            $table->unsignedBigInteger('assigned_to')->nullable()->after('user_id');
            $table->date('due_date')->nullable()->after('assigned_to');
            $table->integer('estimated_effort')->nullable()->after('due_date');
            $table->json('tags')->nullable()->after('estimated_effort');
            $table->boolean('is_public')->default(true)->after('tags');
            $table->boolean('is_featured')->default(false)->after('is_public');
            $table->integer('comment_count')->default(0)->after('is_featured');
            $table->integer('view_count')->default(0)->after('comment_count');
            $table->softDeletes()->after('updated_at');
        });

        // Update existing data
        DB::table('feature_requests')->update([
            'user_id' => 1, // Set to first user
            'slug' => DB::raw('CONCAT("feature-request-", id)'),
            'status' => 'pending',
            'priority' => 'medium',
            'is_public' => true,
            'is_featured' => false,
            'comment_count' => 0,
            'view_count' => 0,
        ]);

        // Now add foreign key constraints
        Schema::table('feature_requests', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('feature_request_categories')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });

        // Drop the old status_id column and foreign key
        Schema::table('feature_requests', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });

        // Rename votes column to vote_count
        Schema::table('feature_requests', function (Blueprint $table) {
            $table->renameColumn('votes', 'vote_count');
        });

        // Add indexes
        Schema::table('feature_requests', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index(['category_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['assigned_to', 'status']);
            $table->index(['is_public', 'status']);
            $table->index(['is_featured', 'created_at']);
            $table->index(['vote_count', 'created_at']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feature_requests', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['category_id', 'status']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['assigned_to', 'status']);
            $table->dropIndex(['is_public', 'status']);
            $table->dropIndex(['is_featured', 'created_at']);
            $table->dropIndex(['vote_count', 'created_at']);
            $table->dropIndex(['due_date', 'status']);

            // Drop foreign keys
            $table->dropForeign(['category_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assigned_to']);

            // Drop added columns
            $table->dropColumn([
                'slug',
                'status',
                'priority',
                'category_id',
                'user_id',
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
            $table->renameColumn('vote_count', 'votes');
        });

        // Re-add status_id column
        Schema::table('feature_requests', function (Blueprint $table) {
            $table->bigInteger('status_id')->unsigned()->after('description');
            $table->foreign('status_id')->references('id')->on('feature_statuses');
        });
    }
};
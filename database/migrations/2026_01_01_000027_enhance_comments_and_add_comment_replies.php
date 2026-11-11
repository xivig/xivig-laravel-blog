<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Enhance comments table with user_id and soft deletes
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('post_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('comments', 'deleted_at')) {
                $table->softDeletes()->after('user_agent');
            }
        });

        // 2. Create comment_replies table if not exists
        if (!Schema::hasTable('comment_replies')) {
            Schema::create('comment_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('comment_id')->constrained('comments')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('reply');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('comment_replies');

        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('comments', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};

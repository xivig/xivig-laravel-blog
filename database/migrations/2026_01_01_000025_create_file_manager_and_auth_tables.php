<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Two-Factor Authentication Columns for Users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'two_factor_code')) {
                $table->string('two_factor_code', 255)->nullable()->after('two_factor_enabled');
            }
            if (!Schema::hasColumn('users', 'two_factor_expires_at')) {
                $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');
            }
        });

        // 2. Password Reset Tokens
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // 3. File Manager Folders
        if (!Schema::hasTable('file_manager_folders')) {
            Schema::create('file_manager_folders', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('parent_id')->nullable()->constrained('file_manager_folders')->onDelete('cascade');
                $table->string('color')->default('primary');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 4. File Manager Files
        if (!Schema::hasTable('file_manager_files')) {
            Schema::create('file_manager_files', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('path');
                $table->string('type')->default('file');
                $table->string('size')->default('0 KB');
                $table->string('icon')->default('bi-file-earmark');
                $table->string('color')->default('primary');
                $table->foreignId('folder_id')->nullable()->constrained('file_manager_folders')->onDelete('cascade');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 5. Calendar Events
        if (!Schema::hasTable('calendar_events')) {
            Schema::create('calendar_events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->dateTime('start_time');
                $table->dateTime('end_time')->nullable();
                $table->boolean('is_all_day')->default(false);
                $table->string('color')->default('primary');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // 6. Task Manager Tasks
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('status')->default('sprint'); // sprint, queue, archived
                $table->string('avatar_badge')->default('AL');
                $table->string('badge_color')->default('#ff4757');
                $table->integer('order')->default(0);
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('calendar_events');
        Schema::dropIfExists('file_manager_files');
        Schema::dropIfExists('file_manager_folders');
        Schema::dropIfExists('password_reset_tokens');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'two_factor_code')) {
                $table->dropColumn('two_factor_code');
            }
            if (Schema::hasColumn('users', 'two_factor_expires_at')) {
                $table->dropColumn('two_factor_expires_at');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Add fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->default('UTC')->after('role');
            }
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation')->nullable()->after('timezone');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('designation');
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('phone');
            }
        });

        // 2. Chat messages table (Live staff & salon chat)
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->foreignId('recipient_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->string('channel')->default('editorial_salon'); // editorial_salon, haute_couture, horology, tech
                $table->text('message');
                $table->string('attachment')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();

                $table->index(['channel', 'created_at']);
                $table->index(['sender_id', 'recipient_id']);
            });
        }

        // 3. Admin emails table (Webmail Inbox / Sent / Drafts / Trash)
        if (!Schema::hasTable('admin_emails')) {
            Schema::create('admin_emails', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('sender_name');
                $table->string('sender_email');
                $table->string('recipient_email');
                $table->string('recipient_name')->nullable();
                $table->string('subject');
                $table->longText('body');
                $table->string('folder')->default('inbox'); // inbox, sent, drafts, trash
                $table->boolean('is_read')->default(false);
                $table->boolean('is_starred')->default(false);
                $table->boolean('is_archived')->default(false);
                $table->string('attachment_path')->nullable();
                $table->string('attachment_name')->nullable();
                $table->unsignedBigInteger('reply_to_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['folder', 'is_read', 'created_at']);
                $table->index('is_starred');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('admin_emails');
        Schema::dropIfExists('chat_messages');

        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            foreach (['two_factor_enabled', 'phone', 'designation', 'timezone'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $cols[] = $col;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

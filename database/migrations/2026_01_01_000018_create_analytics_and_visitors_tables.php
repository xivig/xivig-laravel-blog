<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('visitors')) {
            Schema::create('visitors', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->index();
                $table->string('session_id')->nullable();
                $table->string('device')->default('Desktop');
                $table->string('browser')->default('Chrome');
                $table->string('country_code', 5)->default('FR');
                $table->string('country_name')->default('France');
                $table->string('city')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('first_seen_at')->nullable();
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('page_views')) {
            Schema::create('page_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visitor_id')->nullable()->constrained()->nullOnDelete();
                $table->string('url');
                $table->string('path')->index();
                $table->string('viewable_type')->nullable();
                $table->unsignedBigInteger('viewable_id')->nullable();
                $table->string('referrer')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->index(['viewable_type', 'viewable_id']);
            });
        }

        if (!Schema::hasTable('search_logs')) {
            Schema::create('search_logs', function (Blueprint $table) {
                $table->id();
                $table->string('query')->index();
                $table->integer('results_count')->default(0);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('visitors');
    }
};

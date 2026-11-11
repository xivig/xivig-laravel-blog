<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action'); // created, updated, deleted, login, logout
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->string('description')->nullable();
                $table->json('properties')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('seos')) {
            Schema::create('seos', function (Blueprint $table) {
                $table->id();
                $table->string('seoable_type');
                $table->unsignedBigInteger('seoable_id');
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->string('canonical_url')->nullable();
                $table->string('og_title')->nullable();
                $table->text('og_description')->nullable();
                $table->string('og_image')->nullable();
                $table->string('twitter_card')->default('summary_large_image');
                $table->string('schema_type')->default('Article');
                $table->json('schema_custom')->nullable();
                $table->timestamps();
                $table->unique(['seoable_type', 'seoable_id']);
            });
        }

        if (!Schema::hasTable('backups')) {
            Schema::create('backups', function (Blueprint $table) {
                $table->id();
                $table->string('filename');
                $table->string('disk')->default('local');
                $table->string('type')->default('database'); // database, full, files
                $table->unsignedBigInteger('size_bytes')->default(0);
                $table->string('status')->default('completed');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('revisions')) {
            Schema::create('revisions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('revisionable_type');
                $table->unsignedBigInteger('revisionable_id');
                $table->string('title')->nullable();
                $table->longText('content')->nullable();
                $table->text('summary')->nullable();
                $table->timestamps();
                $table->index(['revisionable_type', 'revisionable_id']);
            });
        }

        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->default('general');
                $table->integer('order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('advertisements')) {
            Schema::create('advertisements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slot')->default('sidebar'); // header, sidebar, in_article, footer
                $table->string('image_path')->nullable();
                $table->string('target_url')->nullable();
                $table->text('custom_code')->nullable();
                $table->integer('impressions')->default(0);
                $table->integer('clicks')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('social_links')) {
            Schema::create('social_links', function (Blueprint $table) {
                $table->id();
                $table->string('platform'); // twitter, instagram, linkedin, youtube, facebook, pinterest
                $table->string('url');
                $table->string('icon')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('revisions');
        Schema::dropIfExists('backups');
        Schema::dropIfExists('seos');
        Schema::dropIfExists('activity_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('widgets')) {
            Schema::create('widgets', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type'); // recent_posts, trending, popular, ads, newsletter, categories, archive, tags, custom_html
                $table->string('area')->default('sidebar'); // sidebar, footer, header
                $table->string('title')->nullable();
                $table->json('settings')->nullable();
                $table->text('content')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('widgets');
    }
};

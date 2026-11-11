<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('galleries')) {
            Schema::create('galleries', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('cover_image')->nullable();
                $table->string('status')->default('active'); // active, draft
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gallery_items')) {
            Schema::create('gallery_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('gallery_id')->constrained('galleries')->cascadeOnDelete();
                $table->string('image_path');
                $table->string('title')->nullable();
                $table->text('caption')->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('galleries');
    }
};

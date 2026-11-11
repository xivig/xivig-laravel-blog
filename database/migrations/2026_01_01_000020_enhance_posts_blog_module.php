<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_sticky')->default(false)->after('is_featured');
            $table->boolean('is_private')->default(false)->after('is_sticky');
            $table->string('password')->nullable()->after('is_private');
        });

        Schema::create('post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('post_images');
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['is_sticky', 'is_private', 'password']);
        });
    }
};

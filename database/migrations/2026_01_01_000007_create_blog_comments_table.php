<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->string('author_name');
            $table->string('author_email');
            $table->string('author_website')->nullable();
            $table->text('content');
            $table->string('status')->default('approved'); // approved, pending, spam, trash
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['post_id', 'status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('comments');
    }
};

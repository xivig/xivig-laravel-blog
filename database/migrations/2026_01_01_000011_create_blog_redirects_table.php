<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_url')->index();
            $table->string('target_url');
            $table->integer('status_code')->default(301);
            $table->unsignedBigInteger('hits')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('redirects');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('location')->unique(); // header, footer, sidebar, mobile
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('title');
                $table->string('url');
                $table->string('target')->default('_self'); // _self, _blank
                $table->string('icon')->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};

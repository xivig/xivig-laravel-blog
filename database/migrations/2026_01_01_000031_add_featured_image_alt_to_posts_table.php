<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('posts') && !Schema::hasColumn('posts', 'featured_image_alt')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('featured_image_alt', 255)->nullable()->after('featured_image_caption');
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('posts') && Schema::hasColumn('posts', 'featured_image_alt')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('featured_image_alt');
            });
        }
    }
};

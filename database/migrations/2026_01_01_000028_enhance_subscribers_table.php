<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            if (!Schema::hasColumn('newsletter_subscribers', 'name')) {
                $table->string('name')->nullable()->after('email');
            }
            if (!Schema::hasColumn('newsletter_subscribers', 'tier')) {
                $table->string('tier')->default('standard')->after('name'); // standard, vip, press, partner
            }
            if (!Schema::hasColumn('newsletter_subscribers', 'source')) {
                $table->string('source')->default('web_portal')->after('ip_address'); // web_portal, admin_manual, csv_import, checkout
            }
            if (!Schema::hasColumn('newsletter_subscribers', 'notes')) {
                $table->text('notes')->nullable()->after('source');
            }
            if (!Schema::hasColumn('newsletter_subscribers', 'last_broadcast_at')) {
                $table->timestamp('last_broadcast_at')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('newsletter_subscribers', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down(): void {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['name', 'tier', 'source', 'notes', 'last_broadcast_at']);
        });
    }
};

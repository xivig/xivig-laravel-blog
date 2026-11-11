<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Editorial & Press'); // Editorial & Press, Creative Direction, Private Curation, Digital Architecture
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->default('bi-gem');
            $table->string('badge')->nullable(); // e.g. "Signature Atelier", "Most Requested", "Exclusive"
            $table->string('cover_image')->nullable();
            $table->string('pricing_type')->default('fixed'); // fixed, starting_at, hourly, retainer, quote
            $table->decimal('price', 10, 2)->nullable();
            $table->string('price_unit')->nullable(); // e.g. "/feature", "/project", "/retainer", "/month"
            $table->string('delivery_time')->nullable(); // e.g. "5-7 Business Days", "2-3 Weeks", "Ongoing"
            $table->json('features')->nullable(); // JSON list of deliverables
            $table->string('target_audience')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('active'); // active, draft, archived
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('service_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->string('budget')->nullable(); // e.g. "< $2,500", "$2,500 - $10,000", "$10,000+"
            $table->string('timeline')->nullable(); // e.g. "Immediate", "Within 1 Month", "Flexible"
            $table->text('message');
            $table->string('status')->default('pending'); // pending, reviewed, contacted, converted, archived
            $table->text('notes')->nullable(); // internal staff notes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('service_inquiries');
        Schema::dropIfExists('services');
    }
};

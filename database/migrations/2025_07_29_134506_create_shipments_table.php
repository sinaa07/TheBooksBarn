<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('tracking_number', 255)->nullable();
            $table->string('carrier', 50)->nullable(); // UPS, FedEx, Local, etc.
            $table->enum('shipment_status', ['preparing', 'shipped', 'in_transit', 'delivered'])->default('preparing');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Indexes
            $table->index('order_id');
            $table->index('tracking_number');
            $table->index('shipment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

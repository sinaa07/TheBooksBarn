<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 17)->unique()->nullable();
            $table->string('title', 255);
            $table->string('author', 255);
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->enum('format', ['hardcover', 'paperback', 'ebook'])->default('paperback');
            $table->string('cover_image_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('featured')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index('category_id');
            $table->index('is_active');
            $table->index('featured');
            $table->index('price');
            $table->index('stock_quantity');
            $table->fullText(['title', 'author', 'description']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};

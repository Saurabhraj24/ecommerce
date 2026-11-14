<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->enum('season', ['winter', 'summer', 'all_season'])->default('all_season');
            $table->string('fabric')->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new_arrival')->default(false);
            $table->boolean('is_best_seller')->default(false);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'category_id']);
            $table->index(['season', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index(['is_new_arrival', 'is_active']);
            $table->index(['is_best_seller', 'is_active']);
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('image_url');
            $table->string('public_id');
            $table->boolean('is_primary')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'is_primary']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('size')->nullable(); // e.g. S, M, L, XL, 32, Free Size
            $table->string('color')->nullable(); // e.g. Maroon, Royal Blue
            $table->string('color_code', 10)->nullable(); // e.g. #800000
            $table->string('sku')->unique();
            $table->decimal('additional_price', 10, 2)->default(0.00);
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
        });

        Schema::create('school_uniforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('school_name');
            $table->string('class_grade'); // e.g. "Class 1st - 5th", "Class 6th - 10th"
            $table->enum('gender', ['boys', 'girls', 'unisex'])->default('unisex');
            $table->string('item_type'); // e.g. "Shirt", "Trouser", "Skirt", "Blazer", "Tracksuit"
            $table->timestamps();

            $table->index(['school_name', 'class_grade', 'gender']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_uniforms');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};

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
    Schema::create('categories', function (Blueprint $table) {
        $table->id('category_id');
        $table->string('category_name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->timestamps();
    });

    Schema::create('suppliers', function (Blueprint $table) {
        $table->id('supplier_id');
        $table->string('supplier_name');
        $table->string('contact_name')->nullable();
        $table->string('address')->nullable();
        $table->string('city')->nullable();
        $table->string('postal_code', 20)->nullable();
        $table->string('country')->nullable();
        $table->string('phone', 40)->nullable();
        $table->timestamps();
    });

    Schema::create('shippers', function (Blueprint $table) {
        $table->id('shipper_id');
        $table->string('shipper_name');
        $table->string('phone', 40)->nullable();
        $table->timestamps();
    });

    Schema::create('customers', function (Blueprint $table) {
        $table->id('customer_id');
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->string('customer_name');
        $table->string('contact_name')->nullable();
        $table->string('address')->nullable();
        $table->string('city')->nullable();
        $table->string('postal_code', 20)->nullable();
        $table->string('country')->nullable();
        $table->string('status')->default('active');
        $table->timestamps();
    });

    Schema::create('products', function (Blueprint $table) {
        $table->id('product_id');
        $table->string('product_name');
        $table->text('description')->nullable();
        $table->foreignId('supplier_id')->nullable()->constrained('suppliers', 'supplier_id')->nullOnDelete();
        $table->foreignId('category_id')->nullable()->constrained('categories', 'category_id')->nullOnDelete();
        $table->string('unit')->nullable();
        $table->decimal('price', 10, 2)->default(0);
        $table->unsignedInteger('stock')->default(0);
        $table->string('photo')->nullable();
        $table->timestamps();
    });

    Schema::create('orders', function (Blueprint $table) {
        $table->id('order_id');
        $table->foreignId('customer_id')->constrained('customers', 'customer_id')->cascadeOnDelete();
        $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();
        $table->date('order_date');
        $table->foreignId('shipper_id')->nullable()->constrained('shippers', 'shipper_id')->nullOnDelete();
        $table->string('status')->default('pending');
        $table->timestamps();
    });

    Schema::create('order_details', function (Blueprint $table) {
        $table->id('order_detail_id');
        $table->foreignId('order_id')->constrained('orders', 'order_id')->cascadeOnDelete();
        $table->foreignId('product_id')->constrained('products', 'product_id');
        $table->unsignedInteger('quantity')->default(1);
        $table->decimal('unit_price', 10, 2)->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('order_details');
    Schema::dropIfExists('orders');
    Schema::dropIfExists('products');
    Schema::dropIfExists('customers');
    Schema::dropIfExists('shippers');
    Schema::dropIfExists('suppliers');
    Schema::dropIfExists('categories');
}
};

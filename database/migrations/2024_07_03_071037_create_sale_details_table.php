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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salesID')->constrained('sales', 'id');
            $table->foreignId('productID')->constrained('products', 'id');
            $table->foreignId('purchase_id')->constrained('purchase_details', 'id');
            $table->float('retail', 10);
            $table->float('purchase_percentage', 10);
            $table->float('sale_percentage', 10);
            $table->float('extra_tax', 10);
            $table->float('pprice', 10);
            $table->float('price', 10);
            $table->float('qty');
            $table->float('amount');
            $table->float('profit');
            $table->date('date');
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};

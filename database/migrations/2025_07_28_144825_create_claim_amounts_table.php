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
        Schema::create('claim_amounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('accounts', 'id')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products', 'id')->cascadeOnDelete();
            $table->float('qty');
            $table->enum('claim_type', ['Item', 'Amount'])->default('Item');
            $table->integer('claim_product_id')->nullable();
            $table->integer('purchase_id')->nullable();
            $table->integer('claim_product_qty')->nullable();
            $table->integer('claim_product_received_in')->nullable();
            $table->float('claim_product_extra_amount')->nullable();
            $table->integer('claim_amount')->nullable();
            $table->enum('claim_amount_is_paid', ['Yes', 'No'])->default('No');
            $table->integer('claim_amount_paid_from')->nullable();
            $table->enum('stock_type', ['Stock-In', 'Scrap'])->default('Stock-In');
            $table->float('stock_weight')->nullable();
            $table->text('scrap_notes')->nullable();
            $table->foreignId('vendor_id')->constrained('accounts', 'id')->cascadeOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->enum('status', ['Pending', 'Completed'])->default('Pending');
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_amounts');
    }
};

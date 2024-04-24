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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('amount_collection',10,2)->nullable()->change();
            $table->string('invoice_number', 50)->unique()->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('amount_collection', 10, 2)->nullable(false)->change();
            $table->dropUnique('invoices_invoice_number_unique');
            $table->string('invoice_number', 50)->change();
        });
    }
};

<?php

use App\Enums\GasTransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gas_transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('document_number', 100)->nullable();
            $table->enum('transaction_type', array_column(GasTransactionType::cases(), 'value'));
            $table->string('evidence_document')->nullable();
            $table->foreignUlid('from_location_id')->nullable()->constrained('gas_locations')->nullOnDelete();
            $table->foreignUlid('to_location_id')->nullable()->constrained('gas_locations')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_transactions');
    }
};

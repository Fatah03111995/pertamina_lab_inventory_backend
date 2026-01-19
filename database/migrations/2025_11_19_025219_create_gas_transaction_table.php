<?php

use App\Enums\GasCylinderStatus;
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
            $table->foreignUlid('gas_cylinder_id')->nullable()->constrained('gas_cylinders')->nullOnDelete();

            //Cascade on Delete
            $table->foreignUlid('header_id')->nullable()->constrained('gas_transaction_headers')->cascadeOnDelete();

            $table->enum('transaction_type', array_column(GasTransactionType::cases(), 'value'))->nullable();
            $table->foreignUlid('from_location_id')->nullable()->constrained('gas_locations')->nullOnDelete();
            $table->foreignUlid('to_location_id')->nullable()->constrained('gas_locations')->nullOnDelete();
            $table->enum('from_status', array_column(GasCylinderStatus::cases(), 'value'));
            $table->enum('to_status', array_column(GasCylinderStatus::cases(), 'value'));
            $table->text('notes')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['gas_cylinder_id', 'transaction_type']);
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

<?php

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gas_events', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('gas_cylinder_id')->nullable()->constrained('gas_cylinders')->nullOnDelete();
            $table->foreignUlid('gas_transaction_id')->nullable()->constrained('gas_transactions')->nullOnDelete();
            $table->enum('event_type', array_column(GasEventType::cases(), 'value'))->nullable();
            $table->foreignUlid('from_location_id')->nullable()->constrained('gas_locations')->nullOnDelete();
            $table->foreignUlid('to_location_id')->nullable()->constrained('gas_locations')->nullOnDelete();
            $table->enum('from_status', array_column(GasCylinderStatus::cases(), 'value'));
            $table->enum('to_status', array_column(GasCylinderStatus::cases(), 'value'));
            $table->text('notes')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['gas_cylinder_id', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_events');
    }
};

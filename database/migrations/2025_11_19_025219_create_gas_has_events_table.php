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
        Schema::create('gas_has_events', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('transaction_id')->nullable()->constrained('gas_transactions')->nullOnDelete();
            $table->foreignUlid('companyId')->nullable()->constrained('gas_companies')->nullOnDelete();
            $table->enum('event_type', array_column(GasEventType::cases(), 'value'))->nullable();
            $table->foreignUlid('location_from_id')->nullable()->constrained('gas_locations')->nullOnDelete();
            $table->foreignUlid('location_to_id')->nullable()->constrained('gas_locations')->nullOnDelete();
            $table->enum('cylinder_status_before', array_column(GasCylinderStatus::cases(), 'value'));
            $table->enum('cylinder_status_after', array_column(GasCylinderStatus::cases(), 'value'));
            $table->text('notes')->nullable()->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_has_events');
    }
};

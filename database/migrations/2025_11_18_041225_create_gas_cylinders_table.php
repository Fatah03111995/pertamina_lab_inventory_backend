<?php

use App\Enums\GasCylinderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gas_cylinders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('gas_type_id')->nullable()->constrained('gas_types')->nullOnDelete();
            $table->string('serial_number', 10);
            $table->string('vendor_code', 100);
            $table->foreignUlid('current_location_id')->nullable()->constrained('gas_locations')->nullOnDelete();
            $table->enum('status', array_column(GasCylinderStatus::cases(), 'value'));
            $table->foreignUlid('company_owner_id')->nullable()->constrained('gas_companies')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_cylinders');
    }
};

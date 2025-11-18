<?php

use App\Enums\GasLocationCategory;
use App\Enums\GasLocationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gas_locations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name', 100);
            $table->enum('type', array_column(GasLocationType::cases(), 'value'));
            $table->enum('category', array_column(GasLocationCategory::cases(), 'value'));
            $table->Text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_locations');
    }
};

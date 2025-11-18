<?php

use App\Enums\GasCompanyCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gas_companies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->enum('category', array_column(GasCompanyCategory::cases(), 'value'));
            $table->text('address');
            $table->string('contact', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_companies');
    }
};

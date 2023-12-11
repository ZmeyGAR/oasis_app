<?php

use App\Models\ContractServices;
use App\Models\ServiceType;
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
        Schema::create('contract_services_service_type', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ContractServices::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(ServiceType::class)->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_services_service_type');
    }
};

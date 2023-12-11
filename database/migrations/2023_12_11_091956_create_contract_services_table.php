<?php

use App\Models\Contract;
use App\Models\State;
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
        Schema::create('contract_services', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Contract::class)->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignIdFor(State::class)->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();

            $table->integer('count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_services');
    }
};

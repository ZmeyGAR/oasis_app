<?php

use App\Models\Contract;
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
        Schema::create('sub_contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Contract::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('number');
            $table->dateTime('date_start')->nullable();
            $table->dateTime('date_end')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_contracts');
    }
};

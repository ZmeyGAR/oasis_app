<?php

use App\Models\CustomerShiping;
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
        Schema::create('shiping_address_coolers', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(CustomerShiping::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('having')->default(false);
            $table->integer('count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shiping_address_coolers');
    }
};

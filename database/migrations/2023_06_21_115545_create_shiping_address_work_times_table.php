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
        Schema::create('shiping_address_work_times', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CustomerShiping::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('start_at')->nullable();
            $table->string('end_at')->nullable();
            $table->string('launch_start_at')->nullable();
            $table->string('launch_end_at')->nullable();
            $table->json('weekend_days')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shiping_address_work_times');
    }
};

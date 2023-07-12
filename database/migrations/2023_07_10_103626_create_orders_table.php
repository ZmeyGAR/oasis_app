<?php

use App\Models\CustomerShiping;
use App\Models\Customer;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('full_address')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('locality')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('house_frontway')->nullable();
            $table->string('house_floor')->nullable();
            $table->string('apartment')->nullable();
            $table->string('intercom_code')->nullable();

            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

<?php

use App\Models\Contract;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contract_services', function (Blueprint $table) {

            if (!Schema::hasColumn('contract_services', 'sub_contract_id')) {
                $table->unsignedBigInteger('sub_contract_id')->nullable();
                $table->foreign('sub_contract_id')
                    ->on('contracts')
                    ->references('id')
                    ->cascadeOnUpdate()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_services', function (Blueprint $table) {

            if (Schema::hasColumn('contract_services', 'sub_contract_id')) {
                DB::statement('SET FOREIGN_KEY_CHECKS = 0');
                $table->dropForeign(['sub_contract_id']);
                $table->dropColumn('sub_contract_id');
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            }
        });
    }
};

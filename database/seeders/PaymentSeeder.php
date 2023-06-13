<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            'cash', 'online_card', 'barcode', 'kaspi', 'bank_check'
        ];

        foreach ($methods as $method) {
            Payment::create([
                'name' => $method
            ]);
        }
    }
}

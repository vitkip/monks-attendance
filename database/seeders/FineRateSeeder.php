<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FineRate;

class FineRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['name' => 'ຂາດໄຫວ້ພຣະ', 'amount' => 10000, 'description' => 'ຂາດການທຳວັດ / ໄຫວ້ພຣະ'],
            ['name' => 'ຂາດສັ່ງສອນ', 'amount' => 5000, 'description' => 'ຂາດຮຽນ / ສັ່ງສອນ'],
            ['name' => 'ຂາດບິນບາດ', 'amount' => 7000, 'description' => 'ຂາດອອກບິນທະບາດ'],
            ['name' => 'ຂາດເຮັດໜ້າທີ່', 'amount' => 3000, 'description' => 'ຂາດເຮັດໜ້າທີ່ທີ່ໄດ້ຮັບມອບໝາຍ'],
        ];

        foreach ($rates as $rate) {
            FineRate::firstOrCreate(['name' => $rate['name']], $rate);
        }
    }
}

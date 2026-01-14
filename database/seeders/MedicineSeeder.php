<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(storage_path('app/public/medicine_prices.json'));
        $data = json_decode($json, true);

        DB::transaction(function () use ($data) {
            foreach ($data['medicines'] as $medicine) {

                // insert / update medicine
                DB::table('medicines')->updateOrInsert(
                    ['id' => $medicine['id']],
                    [
                        'name' => $medicine['name'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                // insert prices
                foreach ($medicine['prices'] ?? [] as $price) {
                    DB::table('medicine_prices')->updateOrInsert(
                        ['id' => $price['id']],
                        [
                            'medicine_id' => $medicine['id'],
                            'unit_price' => $price['unit_price'],
                            'start_date' => $price['start_date']['value'],
                            'end_date' => $price['end_date']['value'],
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            }
        });
    }
}

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
        $json = File::get(storage_path('app/public/medicines.json'));
        $data = json_decode($json, true);

        foreach ($data['medicines'] as $medicine) {
            DB::table('medicines')->updateOrInsert(
                ['id' => $medicine['id']],
                ['name' => $medicine['name']]
            );
        }
    }
}

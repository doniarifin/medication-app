<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $users = [
            [
                'email' => 'admin@example.com',
                'name' => 'Admin',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ],
            [
                'email' => 'dokter@example.com',
                'name' => 'Dokter',
                'role' => 'doctor',
                'password' => Hash::make('dokter123'),
            ],
            [
                'email' => 'apoteker@example.com',
                'name' => 'Apoteker',
                'role' => 'pharmacist',
                'password' => Hash::make('apoteker'),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'id' => Str::uuid(),
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => $user['password'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->call([
            MedicineSeeder::class,
        ]);
    }
}

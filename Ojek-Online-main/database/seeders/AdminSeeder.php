<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            [
                'email' => 'admin@omk.com',  // ← Ganti dengan email yang mudah diingat
            ],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password123'),  // ← Ganti password yang mudah diingat
            ]
        );

        // (Opsional) Tambah admin kedua jika perlu
        // Admin::updateOrCreate(
        //     [
        //         'email' => 'admin2@omk.com',
        //     ],
        //     [
        //         'name'     => 'Admin Kedua',
        //         'password' => Hash::make('password123'),
        //     ]
        // );
    }
}
<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Ir;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::truncate();
        User::factory()->create([
            'email' => 'Admin@gmail.com',
            'username' => 'admin',
            'password' => Hash::make('admin'),
        ]);
        Ir::truncate();
        Ir::create([
            'jumlah' => 1,
            'nilai' => 0
        ]);

        Ir::create([
            'jumlah' => 2,
            'nilai' => 0
        ]);

        Ir::create([
            'jumlah' => 3,
            'nilai' => 0.58
        ]);

        Ir::create([
            'jumlah' => 4,
            'nilai' => 0.9
        ]);
        Ir::create([
            'jumlah' => 5,
            'nilai' => 1.12
        ]);

        Ir::create([
            'jumlah' => 6,
            'nilai' => 1.24
        ]);

        Ir::create([
            'jumlah' => 7,
            'nilai' => 1.32
        ]);

        Ir::create([
            'jumlah' => 8,
            'nilai' => 1.41
        ]);

        Ir::create([
            'jumlah' => 9,
            'nilai' => 1.45
        ]);

        Ir::create([
            'jumlah' => 10,
            'nilai' => 1.49
        ]);

        Ir::create([
            'jumlah' => 11,
            'nilai' => 1.51
        ]);

        Ir::create([
            'jumlah' => 12,
            'nilai' => 1.48
        ]);

        Ir::create([
            'jumlah' => 13,
            'nilai' => 1.56
        ]);

        Ir::create([
            'jumlah' => 14,
            'nilai' => 1.57
        ]);

        Ir::create([
            'jumlah' => 15,
            'nilai' => 1.59
        ]);
    }
}

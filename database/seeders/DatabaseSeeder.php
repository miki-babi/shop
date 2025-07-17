<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     // 'name' => 'Test User',
        //     'username' => 'shopadmin',
        //     'password' => bcrypt('password123'),
        // ]);

        \App\Models\Shop::create(['name' => 'mexico']);
        \App\Models\Shop::create(['name' => 'kadisco']);
        \App\Models\Shop::create(['name' => 'ayat']);
    }
}

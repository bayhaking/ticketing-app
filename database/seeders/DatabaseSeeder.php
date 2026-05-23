<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ini yang akan dipanggil saat kamu ketik --seed
        User::factory()->create([
            'name' => 'Ahmad BAYHAKI',
            'email' => 'bayhaki@spectix.com',
            'password' => bcrypt('password123'), // Kita kunci passwordnya di sini
        ]);
    }
}
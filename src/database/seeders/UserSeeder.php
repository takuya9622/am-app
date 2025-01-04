<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory(15)->create();
    }
}

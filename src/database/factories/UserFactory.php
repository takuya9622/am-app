<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'role' => User::ROLE_USER,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function admin()
    {
        return $this->state([
            'role' => User::ROLE_ADMIN,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
    }

    public function testUser()
    {
        return $this->state([
            'role' => User::ROLE_USER,
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}

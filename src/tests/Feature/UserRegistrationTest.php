<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationFailsWhenNameIsEmpty(): void
    {
        $data = [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->from(route('register'))
        ->post(route('register', $data));

        $response->assertSessionHasErrors(['name']);

        $response->assertRedirect(route('register'));

        $response = $this->get(route('register'));
        $response->assertSeeText('お名前を入力してください');
    }

    public function testRegistrationFailsWhenEmailIsEmpty(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->from(route('register'))
        ->post(route('register', $data));

        $response->assertSessionHasErrors(['email']);

        $response->assertRedirect(route('register'));

        $response = $this->get(route('register'));
        $response->assertSeeText('メールアドレスを入力してください');
    }

    public function testRegistrationFailsWhenPasswordIsEmpty(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ];

        $response = $this->from(route('register'))
        ->post(route('register', $data));

        $response->assertSessionHasErrors(['password']);

        $response->assertRedirect(route('register'));

        $response = $this->get(route('register'));
        $response->assertSeeText('パスワードを入力してください');
    }

    public function testRegistrationFailsWhenNotEnoughPassword(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'password123',
        ];

        $response = $this->from(route('register'))
        ->post(route('register', $data));

        $response->assertSessionHasErrors(['password']);

        $response->assertRedirect(route('register'));

        $response = $this->get(route('register'));
        $response->assertSeeText('パスワードは8文字以上で入力してください');
    }

    public function testRegistrationFailsWhenPasswordDoesNotMatch(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password456',
            'password_confirmation' => 'password123',
        ];

        $response = $this->from(route('register'))
        ->post(route('register', $data));

        $response->assertSessionHasErrors(['password_confirmation']);

        $response->assertRedirect(route('register'));

        $response = $this->get(route('register'));
        $response->assertSeeText('パスワードと一致しません');
    }

    public function testUserRegistrationIsSuccessful(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register', $data));

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
        ]);

        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->name);
        $this->assertTrue(Hash::check('password123', $user->password));

        $response->assertRedirect(route('attendance.index'));

        $this->get(route('attendance.index'))->assertStatus(200);

        $this->assertAuthenticated();
    }
}

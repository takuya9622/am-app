<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginFailsWhenMailIsEmpty(): void
    {
        $this->createTestAdmin();

        $data = [
            'email' => '',
            'password' => 'password123',
        ];

        $response = $this->get(route('admin.login'));
        $response = $this->post(route('admin.login', $data));

        $response->assertSessionHasErrors(['email']);

        $response->assertRedirect(route('admin.login'));

        $response = $this->get(route('admin.login'));
        $response->assertSeeText('メールアドレスを入力してください');
    }

    public function testLoginFailsWhenPasswordIsEmpty(): void
    {
        $this->createTestAdmin();

        $data = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $response = $this->get(route('admin.login'));
        $response = $this->post(route('admin.login', $data));

        $response->assertSessionHasErrors(['password']);

        $response->assertRedirect(route('admin.login'));

        $response = $this->get(route('admin.login'));
        $response->assertSeeText('パスワードを入力してください');
    }

    public function testLoginFailsWhenCredentialsAreInvalid(): void
    {
        $this->createTestAdmin();

        $data = [
            'email' => 'test.forgery@example.com',
            'password' => 'password123',
        ];

        $response = $this->get(route('admin.login'));
        $response = $this->post(route('admin.login', $data));

        $response->assertSessionHasErrors(['email']);

        $response->assertRedirect(route('admin.login'));

        $response = $this->get(route('admin.login'));
        $response->assertSeeText('ログイン情報が登録されていません');
    }

    private function createTestAdmin()
    {
        return User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    }
}

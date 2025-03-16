<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\CreatesTestData;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testLoginFailsWhenMailIsEmpty(): void
    {
        $this->createTestUser();

        $data = [
            'email' => '',
            'password' => 'password123',
        ];

        $response = $this->get(route('login'));
        $response = $this->post(route('login', $data));

        $response->assertSessionHasErrors(['email']);

        $response->assertRedirect(route('login'));

        $response = $this->get(route('login'));
        $response->assertSeeText('メールアドレスを入力してください');
    }

    public function testLoginFailsWhenPasswordIsEmpty(): void
    {
        $this->createTestUser();

        $data = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $response = $this->get(route('login'));
        $response = $this->post(route('login', $data));

        $response->assertSessionHasErrors(['password']);

        $response->assertRedirect(route('login'));

        $response = $this->get(route('login'));
        $response->assertSeeText('パスワードを入力してください');
    }

    public function testLoginFailsWhenCredentialsAreInvalid(): void
    {
        $this->createTestUser();

        $data = [
            'email' => 'test.forgery@example.com',
            'password' => 'password123',
        ];

        $response = $this->get(route('login'));
        $response = $this->post(route('login', $data));

        $response->assertSessionHasErrors(['email']);

        $response->assertRedirect(route('login'));

        $response = $this->get(route('login'));
        $response->assertSeeText('ログイン情報が登録されていません');
    }
}
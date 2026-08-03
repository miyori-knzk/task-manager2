<?php

namespace Tests\Feature;

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 登録画面を表示できる(): void
    {
        $responce = $this->get(route('register'));

        $responce->assertStatus(200);
    }

    /** @test */
    public function 新規ユーザーを登録できる(): void
    {
        $responce = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $responce->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);
        $this->assertAuthenticated();
    }

    /** @test */
    public function 名前が空だとバリデーションエラーになる(): void
    {
        $responce = $this->post(route('register'), [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confermation' => 'password123',
        ]);

        $responce->assertSessionHasErrors('name');
    }

    /** @test */
    public function メールアドレスが空だとバリデーションエラーになる(): void
    {
        $responce = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confermation' => 'password123',
        ]);

        $responce->assertSessionHasErrors('email');
    }

    /** @test */
    public function 無効なメールアドレスだとバリデーションエラーになる(): void
    {
        $responce = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'inveiled-email',
            'password' => 'password123',
            'password_confermation' => 'password123',
        ]);

        $responce->assertSessionHasErrors('email');
    }

    /** @test */
    public function 既に登録済みのメールアドレスだとバリデーションエラーになる(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);

        $responce = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confermation' => 'password123',
        ]);

        $responce->assertSessionHasErrors('email');
    }

    /** @test */
    public function パスワードが8文字未満だとバリデーションエラーになる(): void
    {
        $responce = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'testg@example.com',
            'password' => 'short',
            'password_confermation' => 'short',
        ]);

        $responce->assertSessionHasErrors('password');
    }

    /** @test */
    public function パスワードが一致しないとバリデーションエラーになる(): void
    {
        $responce = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'testg@example.com',
            'password' => 'password123',
            'password_confermation' => 'password',
        ]);

        $responce->assertSessionHasErrors('password');
    }
}

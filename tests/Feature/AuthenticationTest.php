<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン画面を表示できる(): void
    {
        $responce = $this->get(route('login'));

        $responce->assertStatus(200);
    }

    /** @test */
    public function 正しい認証情報でログインできる(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $responce = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $responce->assertRedirect(route('tasks.index'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function 間違ったパスワードではログインできない(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $responce = $this->post(route('login'), [
            'email' => $user->email,
            'password' => bcrypt('worng-password'),
        ]);

        $responce->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function 存在しないメールアドレスではログインできない(): void
    {
        $responce = $this->post(route('login'), [
            'email' => 'nonexistent@exsample.email',
            'password' => 'password123',
        ]);

        $responce->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function メールアドレスが空だとバリデーションエラーになる(): void
    {
        $responce = $this->post(route('login'), [
            'email' => '',
            'password' => 'password123',
        ]);

        $responce->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function パスワードが空だとバリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $responce = $this->post(route('login'), [
            'email' => $user->email,
            'password' => '',
        ]);

        $responce->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /** @test */
    public function ログアウトできる(): void
    {
        $user = User::factory()->create();
        $responce = $this->actingAs($user)->post(route('logout'));

        $responce->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function 認証済みユーザーはログインページにアクセスするとリダイレクトされる(): void
    {
        $user = User::factory()->create();
        $responce = $this->actingAs($user)->post(route('login'));

        $responce->assertRedirect(route('tasks.index'));
    }
}

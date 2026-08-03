<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnauthenticatedRedirectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未認証ユーザーはタスク一覧にアクセスするとログインページにリダイレクトされる(): void
    {
        $responce = $this->get(route('tasks.index'));

        $responce->assertRedirect(route('login'));
    }

    /** @test */
    public function 未認証ユーザーはタスク作成画面にアクセスするとログインページにリダイレクトされる(): void
    {
        $responce = $this->get(route('tasks.create'));

        $responce->assertRedirect(route('login'));
    }

    /** @test */
    public function 未認証ユーザーはカテゴリー一覧にアクセスするとログインページにリダイレクトされる(): void
    {
        $responce = $this->get(route('categories.index'));

        $responce->assertRedirect(route('login'));
    }

    /** @test */
    public function 未認証ユーザーはカテゴリー作成画面にアクセスするとログインページにリダイレクトされる(): void
    {
        $responce = $this->get(route('categories.create'));

        $responce->assertRedirect(route('login'));
    }
}

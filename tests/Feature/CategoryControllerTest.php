<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザーはカテゴリー一覧を取得できる(): void
    {
        $user = User::factory()->create();

        Category::factory()->count(3)->create();

        $responce = $this->actingAs($user)->get(route('categories.index'));

        $responce->assertStatus(200);
        $responce->assertViewHas('categories');
    }

    /** @test */
    public function ユーザーはカテゴリー詳細を取得できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $responce = $this->actingAs($user)->get(route('categories.show', $category));

        $responce->assertStatus(200);
        $responce->assertViewHas('category');
    }

    /** @test */
    public function ユーザーはカテゴリー作成画面を表示できる(): void
    {
        $user = User::factory()->create();

        $responce = $this->actingAs($user)->get(route('categories.create'));

        $responce->assertStatus(200);
    }

    /** @test */
    public function ユーザーはカテゴリーを作成できる(): void
    {
        $user = User::factory()->create();

        $responce = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'テストカテゴリー',
        ]);

        $responce->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'テストカテゴリー',
        ]);

    }

    /** @test */
    public function カテゴリー名が空だとバリデーションエラーになる(): void
    {
        $user = User::factory()->create();

        $responce = $this->actingAs($user)->post(route('categories.store'), [
            'name' => '',
        ]);

        $responce->assertSessionHasErrors('name');

    }

    /** @test */
    public function カテゴリー名は255文字まで入力できる(): void
    {
        $user = User::factory()->create();

        $responce = $this->actingAs($user)->post(route('categories.store'), [
            'name' => str_repeat('あ', 255),
        ]);

        $responce->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => str_repeat('あ', 255),
        ]);
    }

    /** @test */
    public function カテゴリー名は256文字以上だとバリデーションエラーになる(): void
    {
        $user = User::factory()->create();

        $responce = $this->actingAs($user)->post(route('categories.store'), [
            'name' => str_repeat('あ', 256),
        ]);

        $responce->assertSessionHasErrors('name');
    }

    /** @test */
    public function ユーザーはカテゴリー編集画面を表示できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $responce = $this->actingAs($user)->get(route('categories.edit', $category));

        $responce->assertStatus(200);
        $responce->assertViewHas('category');
    }

    /** @test */
    public function ユーザーはカテゴリーを更新できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => '更新前のカテゴリー']);

        $responce = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => '更新後のカテゴリー',
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => '更新後のカテゴリー',
        ]);
    }

    /** @test */
    public function ユーザーはカテゴリーを削除できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $responce = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $responce->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function タスクが紐づいているカテゴリーは削除できない(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $responce = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $responce->assertRedirect(route('categories.index'));
        $responce->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);

    }
}

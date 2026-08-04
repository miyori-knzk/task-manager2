<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タスク一覧を_jso_n形式で取得できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $responce = $this->getJson('/api/tasks');

        $responce->assertStatus(200);
        $responce->assertJsonCount(3, 'data');
    }

    /** @test */
    public function タスク一覧の_jso_nレスポンス構造が正しい(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'テストカテゴリー']);
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'テストタスク',
            'priority' => 2,
        ]);

        $responce = $this->getJson('/api/tasks');

        $responce->assertStatus(200);
        $responce->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'priority',
                    'priority_label',
                    'category' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function タスク一覧の_jso_nレスポンス内容が正しい(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'テストカテゴリー']);
        $taskLow = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => '低優先度タスク',
            'priority' => 1,
        ]);
        $taskMedium = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => '中優先度タスク',
            'priority' => 2,
        ]);

        $responce = $this->getJson('/api/tasks');

        $responce->assertStatus(200);
        $responce->assertJsonFragment([
            'id' => $taskLow->id,
            'title' => '低優先度タスク',
            'priority' => 1,
            'priority_label' => '低',
        ]);
        $responce->assertJsonFragment([
            'id' => $taskMedium->id,
            'title' => '中優先度タスク',
            'priority' => 2,
            'priority_label' => '中',
        ]);
        $responce->assertJsonFragment([
            'name' => 'テストカテゴリー',
        ]);
    }

    /** @test */
    public function タスクが0件の場合は空の配列を返す(): void
    {
        $responce = $this->getJson('/api/tasks');

        $responce->assertStatus(200);
        $responce->assertJsonCount(0, 'data');
        $responce->assertJson(['data' => []]);
    }

    /** @test */
    public function 特定のタスクを_jso_n形式で取得できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'テストカテゴリー']);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'テストタスク',
            'priority' => 2,
        ]);
        $responce = $this->getJson("/api/tasks/{$task->id}");

        $responce->assertStatus(200);
        $responce->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'priority',
                'priority_label',
                'category' => [
                    'id',
                    'name',
                ],
            ],
        ]);
    }

    /** @test */
    public function 特定のタスクの_jso_nレスポンス内容が正しい(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => '仕事']);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => '重要なタスク',
            'priority' => 3,
        ]);
        $responce = $this->getJson("/api/tasks/{$task->id}");

        $responce->assertStatus(200);
        $responce->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => '重要なタスク',
                'priority' => 3,
                'priority_label' => '高',
                'category' => [
                    'id' => $category->id,
                    'name' => '仕事',
                ],
            ],
        ]);
    }

    /** @test */
    public function 存在しないタスク_i_dで404エラーを返す(): void
    {
        $responce = $this->getJson('/api/tasks/99999');

        $responce->assertNotFound();
    }

    /** @test */
    public function 無効なタスク_i_dで404エラーを返す(): void
    {
        $responce = $this->getJson('/api/tasks/invaild');

        $responce->assertStatus(404);
    }
}

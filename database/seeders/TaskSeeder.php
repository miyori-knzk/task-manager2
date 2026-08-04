<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tmp_tasks = [];
        $users = User::all();

        foreach ($users as $user) {
            if ($user->id != 1) {
                $tmp_task = [];
                $tmp_task = [
                    'user_id' => $user->id,
                    'category_id' => mt_rand(1, 3),
                ];

                $tmp_tasks[] = $tmp_task;
            }
        }

        foreach ($tmp_tasks as $key => $val) {
            Task::factory()->create([
                'user_id' => $val['user_id'],
                'category_id' => $val['category_id'],
            ]);
        }

        // テストユーザーのタスクを作成
        Task::factory()->count(3)->create([
            'user_id' => 1,
            'category_id' => mt_rand(1, 3),
        ]);
    }
}

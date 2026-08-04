<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->create([
            'name' => 'プライベート',
        ]);

        Category::factory()->create([
            'name' => '仕事',
        ]);

        Category::factory()->create([
            'name' => 'その他',
        ]);
    }
}

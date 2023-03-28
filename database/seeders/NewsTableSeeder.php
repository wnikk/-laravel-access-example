<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;

class NewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        News::create([
            'user_id' => 1,
            'name' => 'First news',
            'description' => 'Description of first news',
            'body' => 'Body content 1...',
        ]);
        News::create([
            'user_id' => 1,
            'name' => 'Second news',
            'description' => 'Description of second test news',
            'body' => 'Body content 2...',
        ]);
        News::create([
            'user_id' => 2,
            'name' => 'News of test user',
            'body' => 'Body content 3...',
        ]);
    }
}

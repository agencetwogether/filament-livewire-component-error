<?php

namespace bootstrap\database\seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
        ]);

        Post::factory()
            ->has(Comment::factory()->count(5))
            ->count(10)
            ->create();

        Video::factory()
            ->has(Comment::factory()->count(2))
            ->count(4)
            ->create();
    }
}

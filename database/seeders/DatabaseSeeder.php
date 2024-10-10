<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\Mailing;
use App\Models\Post;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
        ]);


        Post::factory()
            ->has(Comment::factory()->count(5))
            ->has(Mailing::factory()->count(2))
            ->count(3)
            ->create();


        Video::factory()
            ->has(Comment::factory()->count(2))
            ->has(Mailing::factory()->count(3))
            ->count(2)
            ->create();
    }
}

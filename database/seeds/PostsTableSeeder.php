<?php

use Illuminate\Database\Seeder;
use App\Models\Post;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::truncate();  // 先清理表数据
        factory(Post::class, 20)->create();  // 一次填充20篇文章
    }
}
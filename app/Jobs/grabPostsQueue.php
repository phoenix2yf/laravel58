<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class grabPostsQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $ret = Post::insert([
                'slug' => uniqid(),
                'title' => $this->data->title,
                'content' => $this->data->content,
                'published_at' => $this->data->updateTime,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo json_encode(['code' => 200, 'msg' => $ret]);
        }catch (\Exception $exception) {
            echo json_encode(['code'=>0,'msg'=>$exception->getMessage()]);
        }
    }
}

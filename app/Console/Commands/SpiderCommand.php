<?php

namespace App\Console\Commands;

use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Pool;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Jobs\grabPostsQueue;
use App\Service\Reptile\AdidasReptile;
use Ramsey\Uuid\Uuid;

class SpiderCommand extends Command
{

    protected $signature = 'command:spider';

    protected $description = 'php spider';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $reptile = new AdidasReptile();
        $reptile->saveGrabData();
        //$reptile->grabList();
        //$this->grabSearch();
    }

    public function grabSearch()
    {   
        $grabSiteUrl = 'https://blog.csdn.net/weixin_33971130/article/list/2';
        $client = new GoutteClient();
        $crawler = $client->request('GET', $grabSiteUrl);
        $crawler->filter('.article-list h4 a')->each(function ($node) {
            $detailUrl = $node->attr('href');
            $this->grabDetail($detailUrl);
        });
    }

    public function grabDetail($url)
    {
        try {
            $client = new GoutteClient();
            $crawler = $client->request('GET', $url);
            $title   = $crawler->filter('.blog-content-box .title-article')->first()->text();
            $content = $crawler->filter('#cnblogs_post_body')->first()->text();
            $updateTime   = $crawler->filter('.bar-content .time')->first()->text();
            if (empty($title) || empty($content)) {
                throw new Exception("node empty", 1);
            }
        } catch (\Exception $e) {
            $this->info($e->getMessage());
            return false;
        }
        
        //$ret = $this->savePostData();

        $ret = new grabPostsQueue([
            'slug' => uniqid(),
            'title' => $title,
            'content' => $content,
            'published_at' => $updateTime,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($ret) {
            $this->info($title . ' 插入成功');
        }
        //var_dump(['title' => $title, 'content' => $content]);
    }

    public function savePostData($data)
    {
        return Post::insert($data);
    }
}
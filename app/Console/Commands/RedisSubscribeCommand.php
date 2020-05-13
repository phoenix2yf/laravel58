<?php

namespace App\Console\Commands;

use App\Base\DelayQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribeCommand extends Command
{

    protected $signature = 'redis:subscribe';

    protected $description = 'redis subscribe';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
       //$this->timingSendMail();
       $this->addMsg();
    }

    public function timingSendMail()
    {
        $a = new DelayQueue('delay_send_mail');
        $a->run(function($msg){
            var_dump($msg);
        });
    }

    public function addMsg()
    {   $time = time() + 5;
        $a = new DelayQueue('delay_send_mail');
        $this->info('time:'. date('Y-m-d H:i:s', $time));
        $a->addTask('订单41343141超时自动取消', $time, ['user_id' => 3213, 'status' => 1, 'time' => date('Y-m-d H:i:s', $time)]);
    }
}
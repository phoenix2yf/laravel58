<?php

namespace App\Console\Commands;

use App;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Base\RabbitmqBase;

class RabbitmqCommand extends Command
{

    protected $signature = 'command:rabbitmq {funcName}';

    protected $description = 'php spider';

    public $channel;
 
    private static $connect;
 
    private static $config;
 
    private static $lastConnectTime;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $funcName = $this->argument('funcName');
        if (!method_exists($this, $funcName)) {
            $this->info("method {$funcName} does't exsit");
            return;
        }
        call_user_func_array([$this, $funcName], []);
    }

    public function sendMsg()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('hello2', false, true, false, false);

        $msg = new AMQPMessage('Hello World!', ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($msg, '', 'hello2');

        $channel->close();
        $connection->close();

        echo " [x] Sent 'Hello World!'\n";
    }


    public function recMsg()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('hello2', false, true, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('hello2', '', false, false, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function sendMsg2()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->exchange_declare('forge', 'direct', false, true, false);
        $msg = new AMQPMessage('Hello World2', ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($msg, 'forge');
        $channel->close();
        $connection->close();
    }

    public function recMsg2()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->exchange_declare('forge', 'direct', false, true, false);
        $channel->queue_declare("grade_posts_1", false, true, true, false);
        $channel->queue_bind('grade_posts_1', 'forge');
        $callback = function($msg){
          echo ' [x] ', $msg->body, "\n";
        };

        $channel->basic_consume('grade_posts_1', '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function recMsg3()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->exchange_declare('forge', 'direct', false, true, false);
        $channel->queue_declare("grade_posts_2", false, true, true, false);
        $channel->queue_bind('grade_posts_2', 'forge');
        $callback = function($msg){
          echo ' [x] ', $msg->body, "\n";
        };

        $channel->basic_consume('grade_posts_2', '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function sendMsg5()
    {
        $rabbitmq = new RabbitmqBase('test_queue');

        for($i=1; $i< 200; $i++){
            $priority = 1;
            $prefixMsg = '';
            if ($i % 5 == 0) {
                $priority = 5;
                $prefixMsg = 'VIP-VIP-';
            }
            if ($i % 3 == 0) {
                $priority = 3;
                $prefixMsg = 'VIP-';
            }
            $rabbitmq->publishedMsg(json_encode(['data' => $prefixMsg . '测试消息', 'priority' => $priority, 'i' => $i]), ['priority' => $priority]);
        }
        
    }

    public function recMsg5()
    {
        $rabbitmq = new RabbitmqBase('test_queue');
        $rabbitmq->basicConsume(function($msg){
            var_dump(json_decode($msg->body, true));
            RabbitmqBase::ack($msg);
            usleep(300000);
        });
    }
}
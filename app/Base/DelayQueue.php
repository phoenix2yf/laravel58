<?php
namespace App\Base;

use Config;
 
class DelayQueue
{
    protected $config = []; //队列配置
    protected $prefix = 'delay_queue:'; //队列前缀
    protected $redis = null; 
    protected $key = ''; //缓存key
    protected $dwellTime = 500; //毫秒
 
    public function __construct($queue)
    {
        $this->config = config('database.redis.default');
        $this->key = $this->prefix . $queue;
        $this->redis = new \Redis;
        $this->redis->connect($this->config['host'], $this->config['port'], $this->config['timeout']);
        $this->redis->auth($this->config['auth']);
    }
 
    public function delTask($value)
    {
        return $this->redis->zRem($this->key, $value);
    }
 
    public function getTask()
    {
        //获取任务，以0和当前时间为区间，返回一条记录
        return $this->redis->zRangeByScore($this->key, 0, time(), ['limit' => [0, 1]]);
    }
 
    public function addTask($name, $time, $data)
    {
        //添加任务，以时间作为score，对任务队列按时间从小到大排序
        return $this->redis->zAdd(
            $this->key,
            $time,
            json_encode([
                'task_name' => $name,
                'task_time' => $time,
                'task_params' => $data,
            ], JSON_UNESCAPED_UNICODE)
        );
    }
 
    public function run($callback)
    {
        while (true) {
            //每次只取一条任务
            $task = $this->getTask();
            if (empty($task)) {
                echo "等待" . $this->dwellTime . "毫秒..." . "\n";
                usleep($this->dwellTime * 1000);
                continue;
            }
     
            $task = $task[0];
            //有并发的可能，这里通过zrem返回值判断谁抢到该任务
            if ($this->delTask($task)) {
                $task = json_decode($task, true);
                //调用回调方法及传递参数，以便在队列消费中处理消息数据
                call_user_func_array($callback, [$task]);
            }
            echo "等待" . $this->dwellTime . "毫秒..." . "\n";
            usleep($this->dwellTime * 1000);
        }
        
        return true;
    }
}
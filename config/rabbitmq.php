<?php

return [

    /*
    |------------------------------------------------
    | RabbitMq Queue
    | 队列配置
    | 随机抽取SERVER
     */

    'common' => [
        'server' => [
            [
                'host' => 'localhost',
                'port' => '5672',
                'user' => 'guest',
                'pass' => 'guest',
                'vhost' => '/',
            ],
            [
                'host' => 'localhost',
                'port' => '5672',
                'user' => 'guest',
                'pass' => 'guest',
                'vhost' => '/',
            ],
        ],

        'info' => [
            'test_queue' => [
                'queue' => 'test_queue.grabdata',
                'exchange' => 'test_queue',
                'durable' => true,
                'queue_option' => [
                    //'x-max-priority' => 10
                ],
            ],
            'grab_data' => [
                'queue' => 'grab_data',
                'exchange' => 'grab_data',
                'durable' => true,
                'exchangeType' => 'direct',
                'queue_option' => [
                    //'x-max-priority' => 10
                ],
            ],
        ],
    ],
];

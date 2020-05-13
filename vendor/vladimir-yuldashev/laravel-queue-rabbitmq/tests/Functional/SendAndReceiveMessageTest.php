<?php

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Tests\Functional;

use Psr\Log\NullLogger;
use PHPUnit\Framework\TestCase;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Enqueue\AmqpLib\AmqpConnectionFactory;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

/**
 * @group functional
 */
class SendAndReceiveMessageTest extends TestCase
{
    public function test()
    {
        $config = [
            'factory_class' => AmqpConnectionFactory::class,
            'dsn' => null,
            'host' => getenv('HOST'),
            'port' => getenv('PORT'),
            'login' => 'guest',
            'password' => 'guest',
            'vhost' => '/',
            'options' => [
                'exchange' => [
                    'name' => null,
                    'declare' => true,
                    'type' => \Interop\Amqp\AmqpTopic::TYPE_DIRECT,
                    'passive' => false,
                    'durable' => true,
                    'auto_delete' => false,
                ],

                'queue' => [
                    'name' => 'default',
                    'declare' => true,
                    'bind' => true,
                    'passive' => false,
                    'durable' => true,
                    'exclusive' => false,
                    'auto_delete' => false,
                    'arguments' => '[]',
                ],
            ],
            'ssl_params' => [
                'ssl_on' => false,
                'cafile' => null,
                'local_cert' => null,
                'local_key' => null,
                'verify_peer' => true,
                'passphrase' => null,
            ],
        ];

        $connector = new RabbitMQConnector(new Dispatcher());
        /** @var RabbitMQQueue $queue */
        $queue = $connector->connect($config);
        $queue->setContainer($this->createDummyContainer());

        // we need it to declare exchange\queue on RabbitMQ side.
        $queue->pushRaw('something');

        $queue->getContext()->purgeQueue($queue->getContext()->createQueue('default'));

        $expectedPayload = __METHOD__.microtime(true);

        $queue->pushRaw($expectedPayload);

        sleep(1);

        $this->assertEquals(1, $queue->size());

        $job = $queue->pop();

        $this->assertInstanceOf(RabbitMQJob::class, $job);
        $this->assertSame($expectedPayload, $job->getRawBody());

        $job->delete();

        $this->assertEquals(0, $queue->size());
    }

    private function createDummyContainer()
    {
        $container = new Container();
        $container['log'] = new NullLogger();

        return $container;
    }
}

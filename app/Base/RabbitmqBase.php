<?php

namespace App\Base;

use Config;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitmqBase{

	public $conn = null; 

	public $channel = null;

	public $queue = null;

	public $exchange = null;

	public $queueInfo = [];

	public $server = [];

	public $durable = true;

	public $exchangeType = '';

	public $exchangeDurable = true;

	public $noAck = false;

	public $routingKey = '';

	public $queueOption = [];

	public function __construct($queueName)
	{
		$configInfo = Config('rabbitmq.common');
		$this->server = $this->getRandomService($configInfo['server']);
		if (empty($this->server)) {
			throw new \Exception("queue server config error", 1);
		}

		$this->queueInfo = $configInfo['info'][$queueName];
		if (!isset($this->queueInfo['exchange']) || empty($this->queueInfo['exchange'])) {
			throw new \Exception("exchange Error", 1);
		} else {
			$this->exchange = $this->queueInfo['exchange'];
		}

		if (!isset($this->queueInfo['queue']) || empty($this->queueInfo['queue'])) {
			throw new \Exception("queue Error", 1);
		} else {
			$this->queue = $this->queueInfo['queue'];
		}

		$this->exchangeType = isset($this->queueInfo['exchangeType']) ? $this->queueInfo['exchangeType'] : AMQPExchangeType::FANOUT;

		$this->checkExchangeType($this->exchangeType);

		$this->durable = isset($this->queueInfo['durable']) ? $this->queueInfo['durable'] : true;

		$this->routingKey = isset($this->queueInfo['routingKey']) ? $this->queueInfo['routingKey'] : '';

		$this->connect();
	} 

	private function connect()
	{
		$this->conn = new AMQPStreamConnection($this->server['host'], $this->server['port'], $this->server['user'], $this->server['pass']);
        $this->channel = $this->conn->channel();

        $this->channel->exchange_declare($this->exchange, $this->exchangeType, false, $this->exchangeDurable, false);

        if ($this->queueInfo['queue_option']) {
        	$this->queueOption = new AMQPTable($this->queueInfo['queue_option']);
        }
        
        $this->channel->queue_declare($this->queue, false, $this->durable, false, false, false, $this->queueOption);

        $this->channel->queue_bind($this->queue, $this->exchange, $this->routingKey);
	}

	public function publishedMsg($msg, $options = [])
	{
		$msg = new AMQPMessage($msg, array_merge([
			'content_type' => 'text/plain', 
			'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
		], $options));
        $this->channel->basic_publish($msg, $this->exchange, $this->routingKey);
	}

	public function basicConsume($callback)
	{	
		$this->channel->basic_qos(null, 1, null);
		$this->channel->basic_consume($this->queue, '', false, $this->noAck, false, false, $callback);

        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
	}

	private function checkExchangeType($type)
	{
		if (!in_array($type, [
			AMQPExchangeType::DIRECT,
			AMQPExchangeType::FANOUT,
			AMQPExchangeType::TOPIC,
			AMQPExchangeType::HEADERS,
		])) {
			throw new \Exception("exchange type error", 1);
		}
	}

	public static function ack($msg)
	{
		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
	}

	private function close()
	{
		$this->channel->close();
        $this->conn->close();
	}

	private function getRandomService($server)
	{
		if (empty($server)) {
			return [];
		}
		return $server[array_rand($server, 1)];
	}
}
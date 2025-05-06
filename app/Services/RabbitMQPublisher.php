<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost')
        );

        $this->channel = $this->connection->channel();
    }

    public function publishModelEvent(Model $model, string $event, string $exchange = 'default_exchange', string $routingKey = null): void
    {
        $routingKey = $routingKey ?: strtolower(class_basename($model)) . '.' . $event;

        $payload = [
            'event' => $event,
            'model' => class_basename($model),
            'data' => $model->toArray(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ];

        $this->channel->exchange_declare($exchange, '', true, false, false);

        $message = new AMQPMessage(json_encode($payload), [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        $this->channel->basic_publish($message, $exchange, $routingKey);
    }

    public function __destruct()
    {
        $this->channel?->close();
        $this->connection?->close();
    }
}

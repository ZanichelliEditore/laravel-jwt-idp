<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 31/10/18
 * Time: 12.38
 */

namespace App\Models\Notification;


use App\Models\Account\User;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher {

    const EXCHANGE_LOGOUT = 'logout';

    private $queues;
    private $user;

    private function __construct(array $queues, User $user){
        $this->queues = $queues;
        $this->user = $user;
    }

    public function send(array $data){

        $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'),
            env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'));
        $channel = $connection->channel();

        $channel->exchange_declare(self::EXCHANGE_LOGOUT, 'fanout', false, false, false);

        foreach ($this->queues as $queue){
            $channel->queue_declare($queue, false, false, false, false);
            $channel->queue_bind($queue, self::EXCHANGE_LOGOUT);
        }

        $msg = new AMQPMessage(json_encode($data), [
            'content_type' => 'application/json'
        ]);

        $channel->basic_publish($msg, self::EXCHANGE_LOGOUT);

        $channel->close();
        $connection->close();
    }

    public static function create(array $queues, User $user){
        return new self($queues, $user);
    }

}
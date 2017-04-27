<?php

namespace yiisolutions\queue;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\base\Component;
use yii\helpers\Json;

class Queue extends Component
{
    public $host;
    public $port;
    public $user;
    public $password;
    public $vhost;

    /**
     * @var AMQPStreamConnection
     */
    private $_connection;

    /**
     * @var AMQPChannel
     */
    private $_channel;

    /**
     * Отправить сообщение в очередь
     *
     * @param string $queue
     * @param array $data
     */
    public function send($queue, array $data)
    {
        $channel = $this->getChannel();
        $channel->queue_declare($queue, false, true, false, false);

        $msg = new AMQPMessage(Json::encode($data), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

        $channel->basic_publish($msg, '', $queue);
    }

    /**
     * Слушать очередь
     *
     * @param string $queue
     */
    public function listen($queue, $callback)
    {
        $channel = $this->getChannel();
        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

    /**
     * Подтверждение канала
     *
     * @param AMQPMessage $msg
     */
    public function acknowledgmentMessage(AMQPMessage $msg)
    {
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    /**
     * @return AMQPChannel
     */
    protected function getChannel()
    {
        if (!$this->_channel) {
            $this->_channel = $this->getConnection()->channel();
        }
        return $this->_channel;
    }

    /**
     * @return AMQPStreamConnection
     */
    protected function getConnection()
    {
        if (!$this->_connection) {
            $this->_connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );
        }
        return $this->_connection;
    }
}
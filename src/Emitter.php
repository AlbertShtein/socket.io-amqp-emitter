<?php

namespace SocketIO;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

define('EVENT', 2);

if (!function_exists('msgpack_pack')) {
    require(__DIR__ . '/msgpack_pack.php');
}

class Emitter
{
    /**
     * @var string
     */
    private $uid = 'emitter';

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $nsp;

    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $exchangeName;

    /**
     * @var string[]
     */
    private $rooms = [];

    /**
     * @var array
     */
    private $flags = [];

    /**
     * @param AMQPStreamConnection $connection
     * @param string $prefix
     * @param string $nsp
     */
    public function __construct(AMQPStreamConnection $connection, $prefix = '', $nsp = '/')
    {
        $this->connection = $connection;
        $this->prefix = $prefix;
        $this->nsp = $nsp;
        $this->channel = sprintf('%s#%s#', $prefix, $nsp);
        $this->exchangeName = sprintf('%s-socket.io', $this->prefix);
    }

    /**
     * @return $this
     */
    public function broadcast()
    {
        $this->flags['broadcast'] = true;

        return $this;
    }

    /**
     * @param string $room
     * @return $this
     */
    public function in($room)
    {
        if (!in_array($room, $this->rooms, true)) {
            $this->rooms[] = $room;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function to($room)
    {
        return $this->in($room);
    }

    /**
     * @param string $nsp
     * @return Emitter
     */
    public function of($nsp)
    {
        return new Emitter($this->connection, $this->prefix, $nsp);
    }

    /**
     * @param array $args
     * @return Emitter
     */
    public function emit(...$args)
    {
        $packet = ['type' => EVENT, 'data' => $args, 'nsp' => $this->nsp];
        $opts = ['rooms' => $this->rooms, 'flags' => $this->flags];
        $msg = msgpack_pack([$this->uid, $packet, $opts]);

        if (empty($this->rooms)) {
            $this->publish($this->channel, $msg);
        } else {
            foreach ($this->rooms as $room) {
                $this->publish(sprintf('%s%s#', $this->channel, $room), $msg);
            }
        }

        $this->rooms = [];
        $this->flags = [];

        return $this;
    }

    /**
     * @param string $chn
     * @param mixed $msg
     * @return void
     */
    private function publish($chn, $msg)
    {
        $this->connection->channel()->basic_publish(
            new AMQPMessage($msg), $this->exchangeName, $chn
        );
    }
}
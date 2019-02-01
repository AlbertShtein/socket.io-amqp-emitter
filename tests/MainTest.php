<?php

namespace Tests;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use SocketIO\Emitter;

class MainTest extends \PHPUnit_Framework_TestCase
{
    public function testMain()
    {
        $c = new AMQPStreamConnection('192.168.1.211', 5672, 'guest', 'guest');
        $e = new Emitter($c);

        $e->of('/v1')->emit('chat', ['fuck' => 'seriously?']);

        $this->assertTrue(true);
    }
}
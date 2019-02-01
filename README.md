socket.io-amqp-emitter
======================

A PHP amqp implementation of socket.io-emitter.

## Usage

### Initialization
```php
$connection = new AMQPStreamConnection('192.168.1.211', 5672, 'guest', 'guest');
$emitter = new Emitter($connection);
$emitter
    ->of('/v1')
    ->to('best')
    ->to('room')
    ->to('ever')
    ->emit('chat', ['everything' => 'is ok?']);
```

### Broadcasting and other flags
Possible flags
* broadcast

```php
$emitter = new Emitter($connection);
$emitter->broadcast->emit('event', 'something else');
```
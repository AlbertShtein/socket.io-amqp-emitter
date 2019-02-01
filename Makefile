TAG := dev

build:
	docker build . -t socketio/amqp/emitter:$(TAG)

exec:
	docker run --rm -it -v /Users/albert/apps/socket.io-amqp-emitter:/var/www/html --name socketio-amqp-emitter-$(TAG) socketio/amqp/emitter:$(TAG) bash

test:
	docker run --rm -it -v /Users/albert/apps/socket.io-amqp-emitter:/var/www/html --name socketio-amqp-emitter-$(TAG) socketio/amqp/emitter:$(TAG) sh -c "composer test"
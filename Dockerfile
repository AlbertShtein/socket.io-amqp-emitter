FROM php:5.6-fpm-jessie

RUN apt-get -y update && apt-get -y install git zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install -j$(nproc) bcmath

WORKDIR /var/www/html
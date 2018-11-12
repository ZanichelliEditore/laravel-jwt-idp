FROM php:7-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev mysql-client openssl zip unzip git \
    && docker-php-ext-install pdo_mysql \
    && pecl install mcrypt-1.0.1 \
    && docker-php-ext-enable mcrypt \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb 

RUN mkdir -p /home/gianluca
RUN groupadd -g 1000 gianluca
RUN useradd -u 1000 -g gianluca gianluca -d /home/gianluca
RUN chown gianluca:gianluca /home/gianluca
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
USER gianluca





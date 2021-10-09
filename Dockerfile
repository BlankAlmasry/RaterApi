FROM php:8.0-fpm
RUN apt-get update -y && apt-get install -y openssl zip unzip git nano
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql
WORKDIR /app

COPY composer.* /app
RUN mkdir resources/passport/src -p && touch resources/passport/src/Client.php
RUN composer install

COPY . /app
RUN php artisan key:generate --force

CMD php artisan serve --host=0.0.0.0 --port=8080
EXPOSE 8080

FROM php:8.1.9-fpm-alpine3.16

# Arguments defined in docker-compose.yml
RUN rm -f /etc/apk/repositories && \
    echo "https://dl-cdn.alpinelinux.org/alpine/v3.16/main" >> /etc/apk/repositories && \
    echo "https://dl-cdn.alpinelinux.org/alpine/v3.16/community" >> /etc/apk/repositories


RUN apk add --update --no-cache ${PHPIZE_DEPS} \
        nginx \
        supervisor\
        dcron \
        zlib \
        zlib-dev \
        libzip\
        libzip-dev \
        libpng \
        libpng-dev \
        gmp-dev \
        jpeg \
        jpeg-dev \
        libjpeg-turbo \
        libjpeg-turbo-dev \
        freetype \
        freetype-dev \
        libxml2 \
        libxml2-dev

RUN docker-php-ext-install -j$(nproc) \
		bcmath \
		mysqli \
		pdo \
		pdo_mysql \
        gmp \
        zip \
        pcntl \
        soap \
        sockets

RUN pecl install redis && \
    docker-php-ext-enable redis && \
    docker-php-ext-configure  opcache --enable-opcache && \
    docker-php-ext-configure  gd --enable-gd --with-freetype --with-jpeg

RUN docker-php-ext-install -j$(getconf _NPROCESSORS_ONLN) gd -j$(getconf _NPROCESSORS_ONLN) gd

# Installing composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm -rf composer-setup.php
RUN apk --update add --virtual build-dependencies build-base openssl-dev autoconf \
  && pecl install mongodb \
  && docker-php-ext-enable mongodb \
  && apk del build-dependencies build-base openssl-dev autoconf \
  && rm -rf /var/cache/apk/*

# Installing
RUN mkdir -p /var/www/html

# Setup Working Dir
WORKDIR /var/www/html
COPY . .
COPY docker/index.php .
#COPY .env.example .env
RUN mkdir -p /var/www/html/storage/logs && \
    touch /var/www/html/storage/logs/laravel.log

RUN chown -R www-data:www-data /var/www/html

# Install project requirements and finish build steps
USER www-data
RUN composer install --prefer-dist --no-dev -o --optimize-autoloader --no-interaction --no-plugins --no-scripts --no-cache

# Prepare nginx and php-fpm configuration
USER root
#PHP : TODO: Check if it works or not
COPY .docker/php/exchange.ini $PHP_INI_DIR/conf.d/exchange.ini
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY .docker/entrypoint.sh /usr/bin/entrypoint.sh
RUN chmod +x /usr/bin/entrypoint.sh

#Supervisord
COPY ./docker/supervisor/exchange.conf /etc/supervisor/conf.d/supervisord.conf
RUN mkdir -p /var/log/supervisor && \
    touch /var/log/supervisor/supervisord.log && \
    chmod -R 777 /var/log/supervisor /var/run

# Setup Crontab
RUN touch crontab.tmp && \
    echo '* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1' >> crontab.tmp && \
    crontab crontab.tmp && \
    rm -rf crontab.tmp

# Run
EXPOSE 80 6000
ENTRYPOINT ["/usr/bin/entrypoint.sh"]

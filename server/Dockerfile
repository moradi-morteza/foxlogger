FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl wget pkg-config libssl-dev libcurl4-openssl-dev zlib1g-dev \
    && pecl download swoole \
    && tar -xf swoole-*.tgz && cd swoole-* \
    && phpize && ./configure --enable-swoole-curl=yes \
    && make -j$(nproc) && make install \
    && docker-php-ext-enable swoole \
    && cd .. && rm -rf swoole-*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --prefer-dist --optimize-autoloader

EXPOSE 9501

CMD ["php", "server.php"]

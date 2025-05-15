FROM composer:2 as composer
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

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer --version && echo "✅ Composer copied and working."

WORKDIR /app

COPY . .

RUN echo "🚀 Running composer install..." && \
    composer install --no-dev --prefer-dist --optimize-autoloader && \
    echo "✅ Composer install completed successfully." \

EXPOSE 9501

CMD ["php", "server.php"]

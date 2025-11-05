FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-dev --no-scripts --prefer-dist --ignore-platform-reqs --optimize-autoloader

FROM php:8.3-fpm AS app

ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    gnupg \
    unixodbc-dev

RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg
RUN curl -fsSL https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN apt-get update && apt-get install -y msodbcsql18 mssql-tools18 && apt-get clean && rm -rf /var/lib/apt/lists/*

ENV PATH="${PATH}:/opt/mssql-tools18/bin"

RUN docker-php-ext-install pdo_mysql exif pcntl bcmath sockets \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

WORKDIR /var/www/html
COPY . .

COPY --from=vendor /app/vendor ./vendor

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN php artisan route:cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 80
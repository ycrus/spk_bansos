FROM php:8.2-fpm
RUN apt-get update && apt-get install -y libssl-dev openssl
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader
CMD ["php-fpm"]
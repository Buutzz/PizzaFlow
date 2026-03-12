FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    cron \
    libpq-dev

RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www

COPY docker/cron/crontab /etc/cron.d/pizza-cron
RUN chmod 0644 /etc/cron.d/pizza-cron
RUN crontab /etc/cron.d/pizza-cron

CMD ["cron", "-f"]
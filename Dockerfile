FROM php:8.1.4-apache

WORKDIR /var/www/html

ARG APP_ENV

ENV DEBIAN_FRONTEND=noninteractive TZ=America/New_York COMPOSER_ALLOW_SUPERUSER=1 APP_ENV=${APP_ENV}

# RUN mv .env.example .env
RUN apt-get update && apt-get install -y zlib1g-dev libzip-dev && \
    docker-php-ext-install zip pdo_mysql && \
    a2enmod rewrite headers

RUN apt-get install -y nano && apt-get install -y netcat && apt-get install -y iputils-ping && apt-get -y install cron && apt install -y git

COPY . .
COPY .git .git
COPY remote.php /var/secret/remote.php

#move the cron jobs command into /etc/crontab
RUN crontab /var/www/html/crontab_file
RUN git config --global --add safe.directory /var/www/html
# Ensure permissions to storage folder.
# RUN chown -R pratyush:pratyush /var/www/html
RUN chown -R www-data:www-data *
RUN chmod -R 755 *
#mark permission
RUN chown www-data:www-data /var/secret/remote.php
RUN chmod 755 /var/secret/remote.php
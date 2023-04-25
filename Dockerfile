
FROM php:7-apache
WORKDIR /var/www/html
ENV TZ=America/New_York
# https://gist.github.com/chronon/95911d21928cff786e306c23e7d1d3f3 for possible docker-php-ext-install values
RUN apt-get update && apt-get install -y zlib1g-dev libzip-dev && \
    docker-php-ext-install zip pdo_mysql && \
    a2enmod rewrite headers cron

RUN docker-php-ext-install sockets
# copy rest of files later to take advantage of cache
COPY . .
#set the cron from cronscript file
RUN crontab cronscript

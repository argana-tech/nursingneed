FROM php:7.1-apache

RUN { \
    echo '[PHP]'; \
    echo 'post_max_size = 1024M'; \
    echo 'upload_max_filesize = 1024M'; \
    echo 'memory_limit = 1024M'; \
    echo 'max_execution_time = 1024'; \
} > /usr/local/etc/php/php.ini

RUN apt-get update \
  && apt-get install -y git zlib1g-dev unzip libpng12-dev libfreetype6-dev libjpeg62-turbo-dev libicu-dev libxml2-dev supervisor \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/lib/x86_64-linux-gnu/ --with-jpeg-dir=/usr/lib/x86_64-linux-gnu/ \
  && docker-php-ext-install pdo_mysql mbstring zip gd intl xml
RUN a2enmod rewrite

RUN { \
    echo '[program:laravel-worker]'; \
    echo 'process_name=%(program_name)s_%(process_num)02d'; \
    echo 'command=php /vagrant/artisan queue:work --sleep=3 --tries=0'; \
    echo 'autostart=true'; \
    echo 'autorestart=true'; \
    echo 'user=root'; \
    echo 'redirect_stderr=true'; \
    echo 'stdout_logfile=/vagrant/storage/logs/worker.log'; \
} > /etc/supervisor/conf.d/laravel-worker.conf

WORKDIR /var/www
RUN rm -rf html && ln -s /vagrant/public html

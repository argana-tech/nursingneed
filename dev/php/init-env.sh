#!/bin/sh

cd /vagrant
php composer.phar install

if [ ! -e .env ]
then
    cp .env.example .env
    php artisan key:generate
fi

php artisan migrate --seed

chmod a+w bootstrap/cache
chmod a+w storage
chmod a+w storage/app
chmod a+w storage/app/public
chmod a+w storage/framework
chmod a+w storage/framework/cache
chmod a+w storage/framework/sessions
chmod a+w storage/framework/views
chmod a+w storage/logs

supervisord

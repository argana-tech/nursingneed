#!/bin/sh

cd /vagrant/dev/php


for cname in `docker ps --filter="name=argana-php" --format "{{.Names}}" -q -a`
do
    if [ "$cname" = argana-php ]
    then
        docker stop $cname
        docker rm $cname
    fi
done

docker build -t argana/php .

docker run \
       -d \
       --restart=always \
       -v /etc/localtime:/etc/localtime:ro \
       --name argana-php \
       --hostname argana-php \
       -p 80:80 \
       -v /vagrant:/vagrant \
       --link argana-mysql:argana-mysql \
       -e DESKTOP_NOTIFIER_SERVER_URL=http://192.168.88.1:12345 \
       argana/php

docker exec argana-php /vagrant/dev/php/init-env.sh

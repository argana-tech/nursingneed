#!/bin/sh

cd /vagrant/dev/mysql

data=false

for cname in `docker ps --filter="name=argana-mysql" --format "{{.Names}}" -q -a`
do
    if [ "$cname" = argana-mysql ]
    then
        docker stop $cname
        docker rm $cname
    fi

    if [ "$cname" = argana-mysql-data ]
    then
        data=true
    fi
done

if [ "$data" = false ]
then
    docker run --name argana-mysql-data -v /var/lib/mysql busybox
fi

docker build -t argana/mysql .

docker run \
       -d \
       --restart=always \
       -v /etc/localtime:/etc/localtime:ro \
       --name argana-mysql \
       --hostname argana-mysql \
       -p 3306:3306 \
       --volumes-from argana-mysql-data \
       -e MYSQL_DATABASE=argana \
       -e MYSQL_USER=argana \
       -e MYSQL_PASSWORD=argana \
       -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
       argana/mysql \
       --character-set-server=utf8 \
       --collation-server=utf8_unicode_ci

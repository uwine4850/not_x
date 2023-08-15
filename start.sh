#!/bin/bash

docker exec -i mysql mysql --defaults-extra-file=/schema/mysql.cnf -e "CREATE DATABASE IF NOT EXISTS not_x;"
docker exec -i mysql mysql --defaults-extra-file=/schema/mysql.cnf < ./schema/users.sql
docker exec -i mysql mysql --defaults-extra-file=/schema/mysql.cnf < ./schema/posts.sql

mkdir "./src/media"
mkdir "./src/media/users"
docker-compose exec php bash -c "chmod -R 777 /var/www/html/media"
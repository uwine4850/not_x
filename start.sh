#!/bin/bash

sudo docker exec -i mysql mysql --defaults-extra-file=/schema/mysql.cnf -e "CREATE DATABASE IF NOT EXISTS not_x;"
sudo docker exec -i mysql mysql --defaults-extra-file=/schema/mysql.cnf < ./schema/users.sql
sudo docker exec -i mysql mysql --defaults-extra-file=/schema/mysql.cnf < ./schema/posts.sql
sudo docker exec -i mysql mysql --defaults-extra-file=/schema/mysql.cnf < ./schema/chat.sql

if [ ! -d "./src/media" ]; then
  mkdir "./src/media"
fi
if [ ! -d "./src/media/users" ]; then
  mkdir "./src/media/users"
fi
sudo docker compose exec php bash -c "chmod -R 777 /var/www/html/media"
sudo docker compose run --rm composer install

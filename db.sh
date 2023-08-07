#!/bin/bash

docker exec -i mysql mysql --defaults-extra-file=/schema/mysql.cnf -e "CREATE DATABASE IF NOT EXISTS not_x;"

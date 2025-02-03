#!/usr/bin/env bash

set -xeu

cd /docker/sae/data

# shellcheck disable=SC1091
. include/.env

sudo git fetch --all
sudo git reset --hard origin/main

for instance in main test; do
    db="sae413_$instance"
    bash sql/unite.bash $instance | sudo docker exec -iw / postgresdb psql -v ON_ERROR_STOP=on -h localhost -wU "$DB_USER" \
        -c "drop database if exists $db" \
        -c "create database $db" \
        -c "\c $db" \
        -f -
done
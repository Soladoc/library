#!/usr/bin/env bash

set -eu

cd /docker/sae/data

sudo git fetch --all
sudo git reset --hard origin/main
sudo git submodule init
sudo git submodule update --remote --merge

# the "sae" database is now an empty placeholder for connection

for instance in main test; do
    db="sae413_$instance"
    bash sql/unite.bash $instance | sudo docker exec -iw / postgresdb psql -v ON_ERROR_STOP=on -h localhost -wU "$DB_USER" \
        -c "drop database if exists $db" \
        -c "create database $db" \
        -c "\c $db" \
        -f -
done
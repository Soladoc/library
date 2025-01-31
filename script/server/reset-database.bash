#!/usr/bin/env bash

set -xeu

cd /docker/sae/data

# shellcheck disable=SC1091
. include/.env

sudo git fetch --all
sudo git reset --hard origin/main

bash sql/unite.bash main | sudo docker exec -iw / postgresdb psql -v ON_ERROR_STOP=on -h localhost -wU "$DB_USER" -d "$DB_NAME"

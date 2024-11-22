#!/usr/bin/env bash

set -xeu

cd /docker/sae/data
sudo git fetch --all
sudo git reset --hard origin/main

cd sql
sudo docker cp . postgresdb:

fargs=()
for f in creaBDD.sql fonctions.sql vuesBDD.sql triggers.util.sql triggers/*.sql bigdata.sql data.sql images.sql offre/*.sql; do
    fargs+=(-f "$f")
done

sudo docker exec -w / postgresdb psql -v ON_ERROR_STOP=on -U sae -d postgres --single-transaction "${fargs[@]}"

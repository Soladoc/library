#!/usr/bin/env bash

# $1: env file contents

set -eua
cd /docker/sae/data
#shellcheck source=/dev/null
. .env
set +a

cd data

sudo git fetch --all
sudo git reset --hard origin/main
sudo git submodule init
sudo git submodule update --remote --merge

cd ..

docker compose -f data/docker-compose.yml down
docker compose -f data/docker-compose.yml up -d

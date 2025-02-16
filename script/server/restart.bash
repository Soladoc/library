#!/usr/bin/env bash

# $1: env files contents

set -eu

cd /docker/sae
echo "$1" > .env
set -a
#shellcheck source=/dev/null
. .env
set +a

docker-compose -f data/docker-compose.yml down
docker-compose -f data/docker-compose.yml up -d

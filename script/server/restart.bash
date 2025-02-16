#!/usr/bin/env bash

# $1: env file contents

set -eua
cd /docker/sae
#shellcheck source=/dev/null
. .env
set +a

docker compose -f data/docker-compose.yml down
docker compose -f data/docker-compose.yml up -d

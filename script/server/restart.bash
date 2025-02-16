#!/usr/bin/env bash

set -eu

cd /docker/sae
echo "$DOTENV" > .env
set -a
#shellcheck source=/dev/null
source .env
set +a

docker-compose -f data/docker-compose.yml down
docker-compose -f data/docker-compose.yml up -d

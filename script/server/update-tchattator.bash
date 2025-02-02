#!/usr/bin/env bash

set -xeu

cd /docker/sae/data
sudo git fetch --all
sudo git reset --hard origin/main

killall tct413

set -a
# shellcheck source=/dev/null
. include/.env
set +a

cd chattator

make -f raphael/v1/Makefile

raphael/v1/bin/tct413 -c config.json

#!/usr/bin/env bash

set -xeu

cd /docker/sae/data
sudo git fetch --all
sudo git reset --hard origin/main

sudo killall tct413 || true

set -a
# shellcheck source=/dev/null
. include/.env
set +a

cd chattator/raphael/v1

sudo apt-get install make -y

sudo make

sudo bin/tct413 -c ../../config.json

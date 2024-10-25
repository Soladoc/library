#!/bin/env bash

set -xeu
cd "$(dirname "${BASH_SOURCE[0]}")"

sudo docker exec web mkdir -p /var/www/html/images_utilisateur

for f in 1 2 3; do
    sudo docker cp $f web:/var/www/html/images_utilisateur/$f
done
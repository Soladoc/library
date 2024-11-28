#!/usr/bin/env bash

#1 Filename of the script to run with root permisions

set -xeuo pipefail

mkdir -p ~/.ssh
echo '${{secrets.ARTIFACT_SSH_KEY}}' >~/.ssh/id_rsa
chmod 600 ~/.ssh/id_rsa
ssh-keyscan -p 22 '${{secrets.ARTIFACT_HOST}}' >>~/.ssh/known_hosts
ssh 'debian@${{secrets.ARTIFACT_HOST}}' sudo bash <"$1"

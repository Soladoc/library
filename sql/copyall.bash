#!/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

cat creaBDD.sql fonctions.sql vuesBDD.sql triggers.sql bigdata.sql data.sql images.sql | xsel -ib
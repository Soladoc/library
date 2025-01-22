#!/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

./code.bash "$@" | psql --single-transaction -v ON_ERROR_STOP=on -h localhost -d raphael -U postgres -w
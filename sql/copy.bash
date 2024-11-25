#!/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

cat_struct=true
cat_data=true

case "${1-}" in
struct) cat_data=false ;;
data) cat_struct=false ;;
esac

{
    echo 'begin;'
    if [[ "$cat_struct" = true ]]; then
        cat schema.sql types.sql creaBDD.sql fonctions.sql vuesBDD.sql triggers.util.sql triggers/*.sql
    fi
    if [[ "$cat_data" = true ]]; then
        cat bigdata.sql data.sql images.sql offre/*.sql
    fi
    echo 'commit;'
} | xsel -ib

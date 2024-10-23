#!/usr/bin/env bash

set -eu
cd "$(dirname "${BASH_SOURCE[0]}")"

for f in *.sql; do
    pg_format -w 120 -u 1 -U 1 -f 1 -p 'WbImport(.|\n)*?;\n' "$f" > "clean-$f"
    mv -f "clean-$f" "$f"
    rm "clean-$f"
done
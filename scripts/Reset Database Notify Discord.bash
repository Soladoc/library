#!/usr/bin/env bash

set -xeo pipefail

readonly failure_jokes=(
    'on attend que @Raph repare ça'
    '@Marius cherche pas les donnees sont perdues'
    "c'est l'heure de la pause café :coffee:"
)
readonly log_lines=10

# Picks a random element in an array.
# $@: the array to choose from
# stdout: the choosen item.
array_pick_random() {
    local i=$((RANDOM % $# + 1))
    echo "${!i}"
}

# The difference in seconds between two date strings.
# $1: date string: first operand
# $2: date string: second operand
# stdout: integer: $1 - $2, in whole seconds.
date_diff() {
    echo $(($(date -d "$1" +%s) - $(date -d "$2" +%s)))
}

# Formats a duration in seconds to the HH:MM:SS format
# $1: integer: duration in seconds
# stdout: string: formatted duration
fmt_hms() {
    local h=0$(($1 / 3600))
    m=0$(($1 / 60 % 60))
    s=0$(($1 % 60))
    echo "${h: -2}:${m: -2}:${s: -2}"
}

# Send a message to the Discord webhook.
# $1: string: the message to send
send_msg() {
    jq -n --arg msg "$1" '{content: { message: $msg }}' |
        curl -H "Content-Type: application/json" -X POST -d @- "$DISCORD_WEBHOOK_URL"
}

# The last lines of the workflow run logs.
# stdout: the last $log_lines lines of the workflow run logs
gh_run_logs() {
    local logs_zip
    logs_zip="$(mktemp)"
    gh >"$logs_zip" api \
        -H "Accept: application/vnd.github+json" \
        -H "X-GitHub-Api-Version: 2022-11-28" \
        "/repos/$REPOSITORY/actions/runs/$RUN_ID/logs"
    unzip -p "$logs_zip" |
        sort |             # sort by timestamp
        colrm 1 29 |       # remove timestamp
        tail -n $log_lines # take last entries
}

mapfile -t prev < <(gh api "repos/$REPOSITORY/actions/runs" --jq "
        .workflow_runs
    | (.[] | select(.id == $RUN_ID }}) | .run_number) as $RUN_NUMBER
    | .[] | select(.workflow_id == $WORKFLOW_ID and .run_number == $RUN_NUMBER - 1
    | .conclusion, .updated_at")
readonly prev_conclusion=${prev[0]}
readonly prev_timestamp=${prev[1]}

if [[ "$CONCLUSION" == failure ]] && [[ "$prev_conclusion" == success ]]; then
    send_msg <<EOF
@everyone $ACTOR a casse la bdd :skull:
$(array_pick_random "${failure_jokes[@]}")

dernieres $log_lines lignes du [log]($HTML_URL) :

\`\`\`log
$(gh_run_logs)
\`\`\`
EOF
elif [[ "$CONCLUSION" == success ]] && [[ "$prev_conclusion" == failure ]]; then
    repair_duration="$(fmt_hms "$(date_diff "$TIMESTAMP" "$prev_timestamp")")"
    send_msg "@everyone Bravo à $ACTOR pour avoir réparé la BDD en $repair_duration :+1:."
fi

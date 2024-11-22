#!/usr/bin/env bash

set -xeuo pipefail

# Functions

# Call GitHub api with pagination, explicit JSON accept and API version headers.
gh-api() {
    gh api --paginate --header 'Accept: application/vnd.github+json' --header 'X-GitHub-Api-Version: 2022-11-28' "$@"
}

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
    local h=0$(($1 / 3600)) m=0$(($1 / 60 % 60)) s=0$(($1 % 60))
    echo "${h: -2}:${m: -2}:${s: -2}"
}

# Send a message to the Discord webhook.
# $1: integer: message flags bitfield (default 0)
# stdin: string: the message to send
send_msg() {
    jq --slurp --raw-input --compact-output --arg flags "${1-0}" '{content:.,$flags}' |
        curl --data @- "$DISCORD_WEBHOOK_URL" \
            --header "Accept: application/json" \
            --header "Content-Type: application/json"
}

# The last lines of the step log.
# $1: integer: the step number
# $2: string: the step name
# $3: integer: tail -n argument
# stdout: the last $log_lines lines of the step log.
gh_job_logs() {
    # Get logs for all jobs
    local logs_zip
    logs_zip="$(mktemp)"
    gh-api "repos/$REPOSITORY/actions/runs/$RUN_ID/logs" >"$logs_zip"

    # Unzip logs
    unzip -p "$logs_zip" "$JOB_ID/${1}_$2" |
        sort |         # sort by timestamp
        tail -n "$3" | # take last entries
        colrm 1 29     # remove timestamp
}

# Constants

readonly failure_jokes=(
    'ET BOUM!'
    'patatrasss'
    'faut dormir'
    'this is fine'
    'allo allo la terre appelle'
    'on attend que @Raph repare ça'
    'chui pas venu ici pour souffrir ok'
    "c'est l'heure de la pause café :coffee:"
    '@Marius cherche pas les données sont perdues'
    'According to all known laws of aviation, there is no way a bee should be able to fly.'
    '**Demoman**: "One crossed wire, one wayward pinch of potassium chlorate, one errant twitch... and KABLOOIE!'
    ':coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee: :coffee:'
)

readonly success_cheers=(
    'Happy happy joy joy!'
    'Fortitudine vincimus.'
    'Moriturus te salutat.'
    '..#]^@^@^@ NO CARRIER'
    'Pulvis et umbra sumus.'
    'Post proelium, praemium.'
    'Ceterum censeo Carthaginem esse delendam.'
)

readonly log_lines=20

# Discord message flag: do not include any embeds when serializing this message
readonly dmf_suppress_embeds=4

# Main logic

# Get previous run info
mapfile -t prev < <(
    gh-api "repos/$REPOSITORY/actions/workflows/$WORKFLOW_ID/runs" --jq "
    .workflow_runs
    | map(select(.id == $RUN_ID))[0].run_number as \$run_number
    | map(select(.run_number < \$run_number
             and (.conclusion == \"success\" or .conclusion == \"failure\"))
         ) | max_by(.run_number)
    | .conclusion, .updated_at"
)
readonly prev_conclusion="${prev[0]}" prev_timestamp="${prev[1]}"
if [[ "$prev_conclusion" == "$CONCLUSION" || -z $prev_conclusion ]]; then
    exit
elif [[ -z $prev_timestamp ]]; then
    echo 'Bug: $prev_timestamp must not be empty'
    exit 99
fi

# Get failed job info
mapfile -t job < <(
    gh-api "repos/$REPOSITORY/actions/runs/$RUN_ID/jobs" --jq '
    .jobs[0] | .html_url, ([.steps[] | select (.conclusion == "failure")][0] | .name, .number)'
)
readonly failed_job_url="${job[0]}" failed_step_name="${job[1]}" failed_step_number="${job[2]}"

link_part="\`$DISPLAY_TITLE\` > \`$failed_step_name\` (step $failed_step_number) ([job]($failed_job_url))"

if [[ "$CONCLUSION" == failure ]]; then
    send_msg $dmf_suppress_embeds <<EOF
@everyone $ACTOR a cassé la BDD :skull:

> $(array_pick_random "${failure_jokes[@]}")

Dernières $log_lines lignes du log :

\`\`\`log
$(gh_job_logs "$failed_step_number" "$failed_step_name" $log_lines)
\`\`\`
EOF
elif [[ "$CONCLUSION" == success ]]; then
    repair_duration="$(fmt_hms "$(date_diff "$TIMESTAMP" "$prev_timestamp")")"
    send_msg $dmf_suppress_embeds <<EOF
@everyone Bravo à $ACTOR pour avoir réparé la BDD en $repair_duration :+1:
$link_part

> $(array_pick_random "${success_cheers[@]}")
EOF
fi

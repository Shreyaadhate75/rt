#!/bin/bash
CRON_JOB="0 9 * * * php $(pwd)/cron.php"
CRON_FILE="mycron"

# Write out current crontab
crontab -l > $CRON_FILE 2>/dev/null

# Echo new cron into cron file
if ! grep -Fxq "$CRON_JOB" $CRON_FILE; then
    echo "$CRON_JOB" >> $CRON_FILE
    crontab $CRON_FILE
    echo "CRON job set."
else
    echo "CRON job already exists."
fi

rm $CRON_FILE

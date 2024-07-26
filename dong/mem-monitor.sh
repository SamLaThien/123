#!/bin/bash

## */10 * * * * bash /home/doanh/tutien-dt-node/mem-monitor.sh
memtotal=$(free | grep Mem | awk '{ print $2 }')
memuse=$(free | grep Mem | awk '{ print $3 }')
let "memusepercent = $memuse * 100 / $memtotal "
let "memtolerance = $memtotal * 7 / 10 "
echo "MemTotal: $memtotal (Usage tolerance: $memtolerance )"
echo "MemUsed: $memuse ($memusepercent %)"
if [ $memuse -ge $memtolerance ]; then
    echo "Memory use over 70%"
    pm2 restart all
fi
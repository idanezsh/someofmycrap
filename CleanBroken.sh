#!/bin/bash
# Copy Broken files of  the day before to a tar file in /tmp 
# 
# Copyright (c) 2016, PineApp Ltd. <http://www.PineApp.com>
#
# Changes:
#
# idan shmueli

DATE=`date +%Y-%m-%d`
YESTERDATE=`date -d '1 day ago' +%Y-%m-%d`
MONTHAGO=`date -d '1 month ago' +%Y-%m`
echo "Removing Broken files from $MONTHAGO only"
SN=`cat /usr/local/etc/PA_SN`
echo $SN

COUNTOLDFILES=`find /var/data/queue/{broken,cur,env,new} -type f  -printf x | grep $MONTHAGO | wc -c`


#********************************************************
echo "Remove Old Broken Files from $MONTHAGO"
echo "files to remove:"
echo `stat -c"%y;%n" /var/data/queue/broken/* | grep "^$DATE" | awk -F';' '{ print $2 }'` > lastremove.log 

echo $DATE >>removedalltimes.log
echo `stat -c"%y;%n" /var/data/queue/broken/* | grep "^$DATE" | awk -F';' '{ print $2 }'` >>removedalltimes.log
# stat -c"%y;%n" /var/data/queue/{broken,cur,env,new,partial}/* | grep -v '^2016-03' | awk -F';' '{ print $2 }' | xargs rm -rf


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
echo "Packing Broken files from $YESTERDATE only"
SN=`cat /usr/local/etc/PA_SN`
echo $SN

COUNTFILES=`find /var/data/queue/broken/ -type f  -printf x | grep $YESTERDATE | wc -c`

if [ "$COUNTFILES" == "0" ]
then
	echo "Start Time: `date`: 0 files found in broken nothing to do" >> /var/log/uploadbrkn2ftp.log
	exit;
else	
	
	CMD= `stat -c"%y;%n" * | grep '^$DATE' | awk -F';' '{ print $2 }' | xargs tar cvf /tmp/"$SN"_"$YESTERDATE"_broken.tar`
	echo $CMD

	HOST='ftp.somethingsomething.com'
	USER='yourid'
	PASSWD='yourpw'
	FILE=`/tmp/"$SN"_"$YESTERDATE"_broken.tar`


	echo Start Time: `date` >> /var/log/uploadbrkn2ftp.log 2>> /var/log/uploadbrkn2ftp.log
	ftp -n $HOST >> /var/log/uploadbrkn2ftp.log 2>> /var/log/uploadbrkn2ftp.log <<SCRIPT
	USER $USER
	$PASSWD
	put $FILE
	quit
SCRIPT

	EXITSTATUS=$?

	if [ $EXITSTATUS == "0" ]
		then
		    echo "Something Something Error in FTP...check /var/log/uploadbrkn2ftp.log"
		    echo $EXITSTATUS >> /var/log/uploadbrkn2ftp.log
		else
		   echo "it's woiking... "
	fi    
echo " removing /tmp/"$SN"_"$YESTERDATE"_broken.tar " >>/var/log/uploadbrkn2ftp.log
rm /tmp/"$SN"_"$YESTERDATE"_broken.tar 2>>/var/log/uploadbrkn2ftp.log

#********************************************************
echo "Remove Old Broken Files from $MONTHAGO"
echo "files to remove:"
echo `stat -c"%y;%n" /var/data/queue/{broken,cur/env/new}/* | grep '^$MONTHAGO' | awk -F';' '{ print $2 }'`
# stat -c"%y;%n" /var/data/queue/{broken,cur/env/new}/* | grep '^2016-03' | awk -F';' '{ print $2 }' | xargs rm
fi


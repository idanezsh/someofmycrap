#!/bin/bash
shopt -s extglob
smtpserver=127.0.0.1
TO="test1@localdomain.com"
CC="test2@localdomain.com,test3@localdomain.com"
FROM="PineApp-Notify@localdomain.com"
subject="CRITICAL data partition usage"
THRESHOLD=1


smtpserver=${smtpserver##*( )}


if [ -f /var/qmail-sys/bin/qmail-inject ]; then
	QMAIL_INJECT="/var/qmail-sys/bin/qmail-inject"
else
	QMAIL_INJECT="/var/qmail/bin/qmail-inject"
fi
	
emailit ()
{
echo -e "From: $FROM
To: $TO
Cc: $CC
Subject: $subject
$body"|$QMAIL_INJECT -h 

}

DATA_PARTITION_SIZE=$(df -h | grep /var/data$ | awk '{ print $5}'| sed 's/%//')

if [ $DATA_PARTITION_SIZE -ge $THRESHOLD ]; then

body="Hi,
	

/var/data partition usage became critical $DATA_PARTITION_SIZE%


PineApp Disk Status Reporter"

emailit

fi


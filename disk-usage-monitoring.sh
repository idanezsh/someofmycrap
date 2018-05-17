#!/bin/bash
shopt -s extglob
smtpserver=127.0.0.1
TO="support@pineapp.com"
CC="shlomis@pineapp.com"
FROM="PineApp-Notify@knesset.gov.il"
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
	CC: $CC
	Subject: $subject
	X-PineApp-Mail-Rcpt-To: $TO
	Content-Type: text/plain;

	$body

	"|$QMAIL_INJECT -h -f $FROM

}



DATA_PARTITION_SIZE=$(df -h | grep /var/data$ | awk '{ print $5}'| sed 's/%//')

if [ $DATA_PARTITION_SIZE -ge $THRESHOLD ]; then

body="Hi,


/var/data partition usage became critical $DATA_PARTITION_SIZE%


PineApp Disk Status Reporter
"

    emailit
fi


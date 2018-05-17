#!/bin/bash

DATE=`date +%Y-%m-%d`
SAFE=`cat /var/log/lastsyncBnW`

if [[ $DATE != $SAFE ]]; then
	#get the file
	wget https://www.usom.gov.tr/zararli-baglantilar/1.html

	echo -n > out
	echo -n > crap
	
	#copy the lines from the HTML according to date
	cat /root/1.html | while read -r line; do
		found=`grep $DATE -B4`
		echo $found >> out
	done
	#cleaning first: breakeline end of line
	sed 's/<\/td>/\n/g' out > out1
	#cleaning second file: start of line
	sed 's/<td>/\n/g' out1 > out
	
	#catch lines with . in them into a different file
	cat /root/out | while read -r line; do
		 if [[ $line == *.* ]]; then
			 echo $line >> crap
		 fi
	done
	
	#inset into DB
	/usr/local/pineapp/pamon.d/mail_policyd.sh stop
	cat crap | while read -r domain; do
	echo $domain
	       	id=`psql secure postgres -t -c "select nextval('policy.black_list_rules_id_seq');"`
        	nextaction=`psql secure postgres -t -c "select nextval('policy.block_actions_id_seq');"`
        	psql secure postgres -c "insert into policy.black_list_rules (id,direction,foreign_address,action_id_type,action_id,forward_id,mirror_id,notify_id,enabled) VALUES ($id,1,'*@$domain',66817,$nextaction,-1,-1,-1,1);"
        	psql secure postgres -c "insert into policy.block_actions (id,block_zone_id) VALUES ($nextaction,1);"
        	psql secure postgres -c "insert into policy.global_black_list_rules (id)VALUES($id);"
	done
	
	/usr/local/pineapp/pamon.d/mail_policyd.sh start
	
	echo $DATE > /var/log/lastsyncBnW

else
	echo "Already did it today, Bye"
fi


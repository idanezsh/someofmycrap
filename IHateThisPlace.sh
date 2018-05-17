#!/bin/bash
    echo -n "Upgrading/Migrating Daily Report"

#    if [ $(psql -A -d secure -U postgres -t -c "SELECT count(*) FROM policy.daily_report_rules;") -gt 0 ]; then
#        echo -e "\t--> \tDaily Report is already upgraded. Skiping ..."
#        return 0
#    fi

    cat /etc/rc.pineapp/rc.spam | grep "=" > /tmp/rc.pineapp.spam
    chmod +x /tmp/rc.pineapp.spam
    . /tmp/rc.pineapp.spam
    rm -f /tmp/rc.pineapp.spam

    if [ "$DAILY_REPORT_RELEASE_BLOCKED_TRAFFIC" == "yes" ]; then
            if [ -z ${RULE_CONFIG} ]; then
                    RULE_CONFIG="0"
            else
                    RULE_CONFIG="$RULE_CONFIG,0"
            fi
    fi
    if [ "$DAILY_REPORT_ALLOWS_PERSONAL_BLACK_WHITE_LIST" == "yes" ]; then
            if [ -z ${RULE_CONFIG} ]; then
                    RULE_CONFIG="1"
            else
                    RULE_CONFIG="$RULE_CONFIG,1"
            fi
    fi
    if [ "$ALLOW_BLACK_AND_WHITE_ACTION_DOMAINS" == "yes" ]; then
            if [ -z ${RULE_CONFIG} ]; then
                    RULE_CONFIG="2"
            else
                    RULE_CONFIG="$RULE_CONFIG,2"
            fi
    fi
    if [ "$DAILY_REPORT_SHOWS_ALL_TRAFFIC" == "no" ]; then
            if [ -z ${RULE_CONFIG} ]; then
                    RULE_CONFIG="4"
            else
                    RULE_CONFIG="$RULE_CONFIG,4"
            fi
    fi
    if [ "$QUICK_LINK_AUTOLOGIN" == "yes" ]; then
            if [ -z ${RULE_CONFIG} ]; then
                    RULE_CONFIG="5"
            else
                    RULE_CONFIG="$RULE_CONFIG,5"
            fi
    fi
    if [ -z ${RULE_CONFIG} ]; then RULE_CONFIG=""; fi
   
    echo "RULE_CONFIG = $RULE_CONFIG"
    if [ "$SEND_USER_DAILY_REPORT" == "yes" ]; then
      for cid in `psql secure postgres -t -c 'SELECT distinct cc.cust_id FROM config.local_domains as cld LEFT JOIN cust.customer as cc ON (cc.cust_id=cld.cust_id)'`; do
        CUST_ID=$cid;
	echo "add domain cust id is: $CUST_ID";
        OWNER_ID=`psql secure postgres -t -c "SELECT distinct cc.owner_id FROM config.local_domains as cld LEFT JOIN cust.customer as cc ON (cc.cust_id=cld.cust_id) where cld.cust_id=$CUST_ID"`;
	echo "add domain owner id is:$OWNER_ID";
        D_ID=`psql secure postgres -t -c "SELECT string_agg(CAST(cld.id as varchar), ',') FROM config.local_domains as cld LEFT JOIN cust.customer as cc ON (cc.cust_id=cld.cust_id) where cld.cust_id=$CUST_ID"`;
	echo "add domain domain ids are: $D_ID";

        psql -d secure -U postgres -t -c "INSERT INTO policy.daily_report_rules (cust_id , owner_id , active , rule_name , rule_description, action_url, lang , rule_type , rule_config , rule_time_send , list_ids) VALUES ($CUST_ID,$OWNER_ID,'t','Daily', 'Domain Company Rule MGR', '$DAILY_REPORT_ACTION_URL', 'en', 2, '{$RULE_CONFIG}','{$REPORT_WILL_BE_SENT_AT}','{$D_ID}');"
	done
	echo "Done Creating Domain Rules"
else
	echo "Set Daily report was set to NO? skipping Domain rules.."
fi
	    	    
	    EXCLUDED_UIDS=$(psql -AR, -d secure -U postgres -t -c "SELECT uid from pineapp.\"userDetails\" where send_spam_report=2;")
        if [ "$EXCLUDED_UIDS" != "" ]; then

	for cid in `psql secure postgres -t -c "SELECT distinct cc.cust_id FROM pineapp.objects as po LEFT JOIN cust.customer as cc ON (cc.cust_id=po.cust_id) LEFT JOIN pineapp.\"userDetails\" as pud on (pud.uid=po.oid) where pud.send_spam_report=2"`; do
        	 CUST_ID=$cid;
	         echo "exclude user cust id is:$CUST_ID";
        	 OWNER_ID=`psql secure postgres -t -c "SELECT distinct cc.owner_id FROM pineapp.objects as po LEFT JOIN cust.customer as cc ON (cc.cust_id=po.cust_id) LEFT JOIN pineapp.\"userDetails\" as pud on (pud.uid=po.oid) where pud.send_spam_report=2 and po.cust_id=$CUST_ID"`;
	         echo "exclude user owner id is:$OWNER_ID";
        	 U_ID=`psql secure postgres -t -c "SELECT string_agg(CAST(po.oid as varchar), ',') FROM pineapp.objects as po LEFT JOIN cust.customer as cc ON (cc.cust_id=po.cust_id) LEFT JOIN pineapp.\"userDetails\" as pud on (pud.uid=po.oid) where pud.send_spam_report=2 and po.cust_id=$CUST_ID"`;
	         echo "these are the uids excludes:$U_ID";
		
            psql -d secure -U postgres -t -c "INSERT INTO policy.daily_report_rules (cust_id , owner_id , active , rule_name , rule_description, action_url, lang , rule_type , rule_config , rule_time_send , list_ids) VALUES ($CUST_ID,$OWNER_ID,'t','Daily', 'Exclude Company Rule MGR', '$DAILY_REPORT_ACTION_URL', 'en', 1, '{$RULE_CONFIG}','{$REPORT_WILL_BE_SENT_AT}','{$U_ID}');"
	done
	echo "Done Creating Exclude users Rules"
else
	echo "no Exclude users?  skipping Exclude users rules.."
		    
    fi

        UIDS=$(psql -AR, -d secure -U postgres -t -c "SELECT uid from pineapp.\"userDetails\" where send_spam_report=1;")
        if [ "$UIDS" != "" ]; then

	for cid in `psql secure postgres -t -c "SELECT distinct cc.cust_id FROM pineapp.objects as po LEFT JOIN cust.customer as cc ON (cc.cust_id=po.cust_id) LEFT JOIN pineapp.\"userDetails\" as pud on (pud.uid=po.oid) where pud.send_spam_report=1"`; do
        	 CUST_ID=$cid;
	         echo "include user cust id is:$CUST_ID";
        	 OWNER_ID=`psql secure postgres -t -c "SELECT distinct cc.owner_id FROM pineapp.objects as po LEFT JOIN cust.customer as cc ON (cc.cust_id=po.cust_id) LEFT JOIN pineapp.\"userDetails\" as pud on (pud.uid=po.oid) where pud.send_spam_report=1 and po.cust_id=$CUST_ID"`;
	         echo "include user owner id is:$OWNER_ID";
	         U_ID=`psql secure postgres -t -c "SELECT string_agg(CAST(po.oid as varchar), ',') FROM pineapp.objects as po LEFT JOIN cust.customer as cc ON (cc.cust_id=po.cust_id) LEFT JOIN pineapp.\"userDetails\" as pud on (pud.uid=po.oid) where pud.send_spam_report=1 and po.cust_id=$CUST_ID"`;
	         echo "include user these are the uids rules:$U_ID";

	        psql -d secure -U postgres -t -c "INSERT INTO policy.daily_report_rules (cust_id , owner_id , active , rule_name , rule_description, action_url, lang , rule_type , rule_config , rule_time_send , list_ids) VALUES ( $CUST_ID , $OWNER_ID ,'t','Daily', 'Report Users Rule MGR', '$DAILY_REPORT_ACTION_URL', 'en', 0, '{$RULE_CONFIG}','{$REPORT_WILL_BE_SENT_AT}','{$U_ID}')";
	 done
	echo "Done Creating User Rules"
else
	echo "No User rules? skipping user rules.."
			 
        fi

    echo "[Finished Daily Report Migration]"
    echo "[Done]"

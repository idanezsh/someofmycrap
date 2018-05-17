#!/bin/bash
clear

#Black        0;30     Dark Gray     1;30
#Red          0;31     Light Red     1;31
#Green        0;32     Light Green   1;32
#Brown/Orange 0;33     Yellow        1;33
#Blue         0;34     Light Blue    1;34
#Purple       0;35     Light Purple  1;35
#Cyan         0;36     Light Cyan    1;36
#Light Gray   0;37     White         1;37
R='\033[0;31m'
CY='\033[0;36m'
G='\033[0;32m'
NC='\033[0m' # No Color

echo -e ${CY}`date`${NC}
echo ""
echo -e "Average load:\t"`cat /proc/loadavg | cut -d" " -f -3`
echo -e "CPU usage:\t"`grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage "%"}'`
echo ""

lic=`cat /usr/local/etc/lic.key | grep End | cut -d'=' -f2`
d=`date +%F`
todate=$(date -d $d +"%Y%m%d")  # = 20130718
cond=$(date -d $lic +"%Y%m%d")    # = 20130715
echo -en "License: "
if [ $todate -gt $cond ]; then
        echo -e "${R}Expired!${NC}"
        else
        echo -e "${G}Valid${NC}"
fi

function check_alive
{
echo -en "$PROCESS: "
CMD=`systemctl status $PROCESS | grep active`
CMD2=`systemctl status $PROCESS | grep active | cut -d';' -f2`
if echo $CMD | grep "running" > /dev/null; then
	echo -ne "${G}OK${NC} Started:$CMD2\n"
	else
	if  echo $CMD | grep "dead" > /dev/null; then
	echo -ne "${R}DEAD!${NC}\n"
	else
        echo -ne "unkonwn - please check manually\n"
        $CMD
        fi  
fi    
}


cat /root/proclist.txt | while read -r line; do
	PROCESS="$line"
	check_alive
done

echo -e "NMAP last find:\t`psql cbwdb postgres -t -c "select time_last from policy.hosts order by time_last desc limit 1;"`" 
echo -e "WMI last find:\t`psql cbwdb postgres -t -c "select time_last from policy.win_hosts order by time_last desc limit 1;"`" 
echo ""
cat /usr/local/cybonet/cbw/rtm/lssvc
echo ""
echo "Suricata last update: " `ls -lu /etc/suricata/rules/suricata.update.md5 |cut -d' ' -f 6-8`
echo -e "Alert Count since up:" `cat /var/log/suricata/stats.log | grep detect.alert | grep Total | tail -n 1 | cut -d'|' -f3`

total=`cat /var/log/suricata/stats.log| grep capture.kernel_packets | grep Total | tail -n1 | cut -d'|' -f3 | cut -d ' ' -f2`
drops=`cat /var/log/suricata/stats.log| grep capture.kernel_drops | grep Total | tail -n1 | cut -d'|' -f3 | cut -d ' ' -f2`
echo -ne "Packet Loss:"
if [ -z $drops ]; then
	echo -e "${G}No Packet Loss${NC}"
	else
	percent=$(((100*$drops)/$total))
	echo -e " $percent%"
fi




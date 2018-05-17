#!/bin/bash
set -u

echo -e "Starting Update in 3 seconds.."
sleep 1
echo -e "2"
sleep 1 
echo -e "1"
sleep 1

echo -e "Stopping CBWUI"
systemctl stop cbwui
systemctl stop cbwui

#Backup Previous RPM + Remove
mv -f /usr/local/cybonet/ui{,.backup.$(date +"%Y%m%d_%H%M%S")}
ls -al /usr/local/cybonet/| grep back

echo -e "Backup Complete"
echo -e "Removing RPM (ignore errors about missing files)"
echo
rpm -e cbw-ui-0.378-1.noarch
echo -e "Done"

echo -e
echo -e "+++++++++++++++++++++++++++"
echo -e

#Install new RPM
echo -e "Installing NEW RPM"

rpm -Uvh /tmp/cbw-ui-0.378-1.noarch.rpm --nodeps --force

echo -e "Done"

echo -e "Restarting CBWUI and HTTPD"
systemctl restart cbwui
systemctl restart httpd
echo -e
echo -e
echo -e "UPDATE COMPLETE"


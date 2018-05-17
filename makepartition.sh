fdisk -l 

echo
echo "***************"
echo

echo -e "type HDD to create and press ENTER (usualy /dev/sdb )"

read hdd

echo
echo "***************"
echo

echo -e "adding HDD as $hdd"


echo -e "d\n\n\nn\np\n\n\n\nw" | fdisk $hdd

udevstart

echo "formatting...."

mke2fs \-j $hdd"1"

echo "format done"



echo "stoping MailSecure Services"
iptables -A OUTPUT -p tcp --dport 25 -j REJECT
iptables -A INPUT -p tcp --dport 25 -j REJECT


monit unmonitor postgres
/etc/rc.d/pgres stop
/etc/rc.d/pgres stop
killall -9 postgres

echo "done"

echo "adding to fstab"
echo "$hdd"1"       /var/data/quarantine   ext3 defaults  1     1" >> /etc/fstab


cat /etc/fstab

echo "moving quarantine .."
mv /var/data/quarantine /var/data/quarantine_

mkdir /var/data/quarantine

mount \-a
cp \-apv /var/data/quarantine_/* /var/data/quarantine/

chown qmailq:qmail /var/data/quarantine

echo "done" 


echo "opening ports and resturing functionality"
iptables -D OUTPUT -p tcp --dport 25 -j REJECT
iptables -D INPUT -p tcp --dport 25 -j REJECT

monit monitor postgres
/etc/rc.d/pgres start

monit validate

echo "Done"



echo "starting to Delete Giverish files"
ls -i1 / | while read -r myinode myname; do if [[ ! -d /"$myname" ]] && [[ ! "$myname" = fcron* ]] ; then echo "$myinode"; fi; done | while read inode; do find / -maxdepth 1 -inum $inode -exec rm -v {} \; ; done

echo "finished Deleting"
exit

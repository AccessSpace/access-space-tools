#!/bin/bash

# The "deal" a background image out code
if [ "$(ls /home/tech/login_screens_working/ | wc -l)" == 0 ]; then
  cp /home/tech/login_screens/* /home/tech/login_screens_working/
fi
background="$(ls /home/tech/login_screens_working/ | sed -n "1p")"
echo $background
mv "/home/tech/login_screens_working/$background" /usr/share/gdm/themes/accessspace/background.jpg

chgrp techgroup /usr/share/gdm/themes/accessspace/background.jpg
chmod 775 /usr/share/gdm/themes/accessspace/background.jpg

cp /home/tech/deb_scripts/log_login.sh /etc/gdm/PostLogin/$HOSTNAME
cp /home/tech/deb_scripts/log_logout.sh /etc/gdm/PostSession/$HOSTNAME


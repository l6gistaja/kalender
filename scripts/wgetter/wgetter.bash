#!/bin/bash

# creates static version of kalender's data
# 1) in web_init.php, set $init_data['dynamic'] = false
# 2) set $baseurl below
# 3) run current script (in directory scripts/wgetter/)
# 4) copy created files in kalender directory back to $baseurl directory
# 5) in $baseurl web_init.php, set $init_data['dynamic'] = false, so newly
#    overwritten static files will be used

baseurl="http://juks.alkohol.ee/etc/trash/kalender"

wget ${baseurl}/ruunid.php
mv -f ruunid.php ../../ruunid.html
wget ${baseurl}/tahtpaevad.php
mv -f tahtpaevad.php ../../tahtpaevad.html
wget ${baseurl}/abi.php
mv -f abi.php ../../abi.html
wget ${baseurl}/calendar_data.php?wgetter=1
mv -f 'calendar_data.php?wgetter=1' ../../includes/calendar_data_static.php

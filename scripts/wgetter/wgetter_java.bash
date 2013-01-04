#!/bin/bash

# creates static version of kalender's data for  j(ava)kalender project
# 1) in web_init.php, set $init_data['dynamic'] = false
# 2) set $baseurl below
# 3) run current script (in directory scripts/wgetter/)

baseurl="http://juks.alkohol.ee/etc/trash/kalender"
javadir="sirvid" 

rm -rf ${javadir}/
mkdir ${javadir}
wget ${baseurl}/ruunid.php?java=1
mv -f 'ruunid.php?java=1' ${javadir}/ruunid.html
wget ${baseurl}/tahtpaevad.php?java=1
mv -f 'tahtpaevad.php?java=1' ${javadir}/tahtpaevad.html
wget ${baseurl}/abi.php?java=1
mv -f 'abi.php?java=1' ${javadir}/abi.html

cp ../../kalender.sdb ${javadir}/
cp ../../descriptions.sdb ${javadir}/
cp ../../kalender.css ${javadir}/
cp ../../kalender.js ${javadir}/
cp ../../lp.gif ${javadir}/
cp ../../est.gif ${javadir}/
cp ../../favicon.ico ${javadir}/
cp -r ../../svg/ ${javadir}/
cp -r ../../txt/ ${javadir}/
cp -r ../../kirikukalender/ ${javadir}/
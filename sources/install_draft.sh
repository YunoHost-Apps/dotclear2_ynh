#!/bin/bash
#Brouillon fonctionnel de script d'installation silencieuse de DotClear2, sur un serveur pourvu d'une base MySQL et d'un nginx pointant sur /var/www/dotclear

# dépendances
apt install -y uuid-runtime curl wget

# paramètres
DC_DBDRIVER="mysqli"
DC_DBHOST="localhost"
DC_DBUSER="dotclear"
DC_DBPASSWORD="dotclear"
DC_DBNAME="dotclear"
DC_MASTER_KEY=`uuidgen`
DC_ADMIN_URL="https://vagrant.test/admin/index.php"
EMAIL="root@localhost"
FIRSTNAME="Rémy"
NAME="Garrigue"
LOGIN="remy"
PWD="dotclear"
TZ="Europe/Paris"
cd /var/www/
CONF="dotclear/inc/config.php"

# sources
wget http://download.dotclear.org/latest.tar.gz -O dotclear.tgz
tar xf dotclear.tgz
rm -f dotclear.tgz
chown www-data:www-data -R dotclear

# sql
# create database dotclear;
# grant all privileges on dotclear.* to dotclear@'localhost' identified by 'dotclear';

# config admin/install/wizard.php
mv $CONF.in $CONF
sed -i -e "s;'DC_DBDRIVER','';'DC_DBDRIVER','$DC_DBDRIVER';" $CONF
sed -i -e "s;'DC_DBHOST','';'DC_DBHOST','$DC_DBHOST';" $CONF
sed -i -e "s;'DC_DBUSER','';'DC_DBUSER','$DC_DBUSER';" $CONF
sed -i -e "s;'DC_DBPASSWORD','';'DC_DBPASSWORD','$DC_DBPASSWORD';" $CONF
sed -i -e "s;'DC_DBNAME','';'DC_DBNAME','$DC_DBNAME';" $CONF
#sed -i -e "s;'DC_DBPREFIX','';'DC_DBPREFIX','dc_';" $CONF
sed -i -e "s;'DC_MASTER_KEY','';'DC_MASTER_KEY','$DC_MASTER_KEY';" $CONF
sed -i -e "s;'DC_ADMIN_URL','';'DC_ADMIN_URL','$DC_ADMIN_URL';" $CONF
sed -i -e "s;'DC_ADMIN_MAILFROM','';'DC_ADMIN_MAILFROM','$EMAIL';" $CONF

# config admin/install/index.php
CURL=`curl -F "u_email=$EMAIL" -F "u_firstname=$FIRSTNAME" -F "u_name=$NAME" -F "u_login=$LOGIN" -F "u_pwd=$PWD" -F "u_pwd2=$PWD" -F "u_date=$TZ" http://vagrant.test/admin/install/index.php`

if [ `echo $CURL | grep -c success` -ge 0 ]
then exit 0
else exit 1
fi



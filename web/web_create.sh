#!/bin/sh

LOG=/etc/httpd/conf/listi-vefja.log
WEBCREATEDIR=/etc/httpd/conf/web_create/
WEBCREATE=$WEBCREATEDIR/web_create.tmp

function logger() {
	local DAGS=`date +'%Y%m%d %H:%M:%S'`
	local SKILABOD=$*
	echo "$DAGS -- $SKILABOD" >> $LOG 2>&1
}

######################
## S�ki config

. $WEBCREATE

export NEW_DOMAIN NEW_HOST NEW_USER NEW_PASS NEW_CONTACT
if [ "x$NEW_HOST" = "x" ]; then
	export NEW_HOST=www
fi

export DAGS=`date`

logger "BEGIN newuser.sh"
logger "CONFIG: NEW_DOMAIN $NEW_DOMAIN"
logger "CONFIG: NEW_HOST   $NEW_HOST"
logger "CONFIG: NEW_USER   $NEW_USER"
logger "CONFIG: NEW_PASS   $NEW_PASS"
logger "CONFIG: NEW_CONTACT   $NEW_CONTACT"
logger "VEFUR: ${NEW_HOST}.${NEW_DOMAIN} - ${NEW_USER} - ${NEW_PASS} - $NEW_CONTACT"

######################
## B� til notandann

logger "Bý til heimasvæði og notanda $NEW_USER"

HOMEDIR=/var/www/virtual/$NEW_HOST.$NEW_DOMAIN
SALT=`dd if=/dev/urandom count=1 2>/dev/null | perl -MMIME::Base64 -0777 -ne 'print encode_base64($_)' |head -1 |tr A-Z a-z | tr -s a-z | cut -b -2`
export NEW_CRYPTPASS=`/usr/local/bin/cryptpw $NEW_PASS $SALT`

/bin/mkdir -p $HOMEDIR/html
/usr/sbin/useradd -c "$NEW_HOST.$NEW_DOMAIN - LAMP Vefur" -d $HOMEDIR -M -g ftpuser -s /bin/false -p $NEW_CRYPTPASS $NEW_USER
/bin/chown -R $NEW_USER:ftpuser $HOMEDIR

######################
## Apache / HTTP

VIRTHOST_DIR=/etc/httpd/conf/virtual-php
VIRTHOST_TEMPLATE=$VIRTHOST_DIR/virtualhost-template
VIRTHOST_NEWCONF=$VIRTHOST_DIR/$NEW_HOST.$NEW_DOMAIN.conf

logger "Bý til Apache config skrá $VIRTHOST_NEWCONF"

/bin/cp $VIRTHOST_TEMPLATE $VIRTHOST_NEWCONF
perl -pi -e "s/___HOST___/$NEW_HOST/g" $VIRTHOST_NEWCONF
perl -pi -e "s/___DOMAIN___/$NEW_DOMAIN/g" $VIRTHOST_NEWCONF
perl -pi -e "s/___DATE___/$NEW_DOMAIN/g" $VIRTHOST_NEWCONF

logger "Set $NEW_HOST.$NEW_DOMAIN í /etc/hosts"

echo "# $NEW_HOST.$NEW_DOMAIN búið til þann $DAGS" >> /etc/hosts
echo "ip             $NEW_DOMAIN $NEW_HOST.$NEW_DOMAIN" >> /etc/hosts
echo "" >> /etc/hosts

######################
## MySQL

logger "Bý til MySQL notanda og gagnagrunn $NEW_USER"

/usr/bin/mysqladmin -uroot -p create $NEW_USER
echo "GRANT ALL PRIVILEGES ON ${NEW_USER}.* TO ${NEW_USER}@localhost IDENTIFIED BY '${NEW_PASS}';" | mysql -uroot -p

######################
## Set vél í webalizer vöktun

#echo "$NEW_HOST.$NEW_DOMAIN" >> /home/webalizer/webs.txt

######################
## Tek afrit af config skrá

DAGSCONF=`date +'%Y%m%d-%H%M%S'`
/bin/cp $WEBCREATE $WEBCREATEDIR/${DAGSCONF}.${NEW_HOST}.${NEW_DOMAIN}
/bin/chmod 600 $WEBCREATEDIR/${DAGSCONF}.${NEW_HOST}.${NEW_DOMAIN}
/bin/rm $WEBCREATE
/bin/touch $WEBCREATE
/bin/chmod 666 $WEBCREATE
/bin/chown apache:apache $WEBCREATE

logger "Endurræsi Apache"
/usr/sbin/apachectl graceful

logger "END newuser.sh"

